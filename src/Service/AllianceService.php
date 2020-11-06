<?php

namespace App\Service;

use App\Entity\Alliance;
use App\Entity\Candidate;
use App\Entity\Member;
use App\Entity\Player;
use App\Entity\User;
use App\Exception\CannotDemoteLeaderException;
use App\Exception\CannotDestroyNonemptyAllianceException;
use App\Exception\CannotPromoteLeaderException;
use App\Exception\LeaderCannotLeaveException;
use App\Exception\TooManyAlliancesException;
use App\Repository\AllianceRepository;
use App\Repository\CandidateRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * All actions related to Alliance Management
 */
class AllianceService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var AllianceRepository
     */
    private AllianceRepository $allianceRepository;

    /**
     * @var MemberRepository
     */
    private MemberRepository $memberRepository;

    /**
     * @var CandidateRepository
     */
    private CandidateRepository $candidateRepository;

    /**
     * AllianceService constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AllianceRepository $allianceRepository,
        MemberRepository $memberRepository,
        CandidateRepository $candidateRepository
    ) {
        $this->entityManager = $entityManager;
        $this->allianceRepository = $allianceRepository;
        $this->memberRepository = $memberRepository;
        $this->candidateRepository = $candidateRepository;
    }

    /**
     * Create an Alliance with this Player as its leader
     *
     * @param Player $player
     * @throws TooManyAlliancesException
     */
    public function createAlliance(Player $player, string $tag, string $name): Alliance
    {
        if ($player->getMember() instanceof Member) {
            throw new TooManyAlliancesException($player->getId());
        }

        $alliance = new Alliance();
        $alliance->setTag($tag);
        $alliance->setName($name);
        $this->entityManager->persist($alliance);

        $member = new Member();
        $member->setAlliance($alliance);
        $member->setPlayer($player);
        $member->setRole(Member::ROLE_LEADER);
        $this->entityManager->persist($member);

        $player->setMember($member);

        $this->entityManager->flush();

        return $alliance;
    }

    /**
     * @param Alliance $alliance
     * @throws CannotDestroyNonemptyAllianceException
     */
    public function destroyAlliance(Alliance $alliance)
    {
        if (count($alliance->getMembers()) > 1) {
            throw new CannotDestroyNonemptyAllianceException($alliance->getId());
        }

        foreach ($alliance->getMembers() as $member) {
            $member->getPlayer()->setMember(null);
            $this->entityManager->remove($member);
        }

        foreach ($alliance->getCandidates() as $candidate) {
            $this->entityManager->remove($candidate);
        }

        $this->entityManager->remove($alliance);
        $this->entityManager->flush();
    }

    /**
     * Promote a Member of the Alliance as Officer
     *
     * @param Member $member
     * @throws CannotPromoteLeaderException
     */
    public function promoteMember(Member $member): void
    {
        if ($member->getRole() === Member::ROLE_LEADER) {
            throw new CannotPromoteLeaderException($member->getId());
        }

        $member->setRole(Member::ROLE_OFFICER);
        $this->entityManager->flush();
    }

    /**
     * Demote an Office of the Alliance as Member
     *
     * @param Member $member
     * @throws CannotDemoteLeaderException
     */
    public function demoteOfficer(Member $member): void
    {
        if ($member->getRole() === Member::ROLE_LEADER) {
            throw new CannotDemoteLeaderException($member->getId());
        }

        $member->setRole(Member::ROLE_MEMBER);
        $this->entityManager->flush();
    }

    /**
     * Create a Candidate for this Player in this Alliance
     *
     * @param Player   $player
     * @param Alliance $alliance
     * @return Candidate
     */
    public function applyToAlliance(Player $player, Alliance $alliance): Candidate
    {
        $candidate = new Candidate();
        $candidate->setPlayer($player);
        $candidate->setAlliance($alliance);

        $this->entityManager->persist($candidate);
        $this->entityManager->flush();

        return $candidate;
    }

    /**
     * Return the list of all Candidates to this Alliance
     *
     * @param bool $includeRejected
     * @return Candidate[]
     */
    public function listCandidates(Alliance $alliance, bool $includeRejected = false): array
    {
        $criteria = ['alliance' => $alliance];

        if ($includeRejected === false) {
            $criteria['isRejected'] = false;
        }

        return $this->candidateRepository->findBy($criteria);
    }

    /**
     * Accept a Candidate, making them a new Member
     *
     * @param Candidate $candidate
     * @return Member
     */
    public function acceptCandidate(Candidate $candidate): Member
    {
        $player = $candidate->getPlayer();
        $oldMember = $player->getMember();

        if ($oldMember instanceof Member) {
            if ($oldMember->getRole() === Member::ROLE_LEADER) {
                throw new LeaderCannotLeaveException($oldMember->getId());
            }

            $this->entityManager->remove($oldMember);
        }

        $member = new Member();
        $member->setAlliance($candidate->getAlliance());
        $member->setPlayer($player);
        $member->setRole(Member::ROLE_MEMBER);
        $this->entityManager->persist($member);

        $player->setMember($member);

        $this->entityManager->remove($candidate);

        $this->entityManager->flush();

        return $member;
    }

    /**
     * Reject a Candidate
     *
     * @param Candidate $candidate
     */
    public function rejectCandidate(Candidate $candidate): void
    {
        $candidate->setIsRejected(true);

        $this->entityManager->flush();
    }

    /**
     * Return the list of all Members, optionally filtered for a battlegroup
     *
     * @param int|null $battlegroup
     * @return Member[]
     */
    public function listMembers(Alliance $alliance, int $battlegroup = null): array
    {
        $criteria = ['alliance' => $alliance];

        if ($battlegroup !== null) {
            $criteria['battlegroup'] = $battlegroup;
        }

        return $this->memberRepository->findBy($criteria, ['role' => 'ASC']);
    }

    /**
     * Remove a Player from an Alliance
     *
     * @param Member $member
     * @throws LeaderCannotLeaveException
     */
    public function removeMember(Member $member)
    {
        if ($member->getRole() === Member::ROLE_LEADER) {
            throw new LeaderCannotLeaveException($member->getId());
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();
    }

    /**
     * Transfer Alliance leadership to another Member
     *
     * @param Member $member
     */
    public function transferLeadership(Member $member): void
    {
        $leader = $this->memberRepository->findOneBy([
            'alliance' => $member->getAlliance(),
            'role' => Member::ROLE_LEADER
        ]);
        $leader->setRole(Member::ROLE_OFFICER);
        $member->setRole(Member::ROLE_LEADER);

        $this->entityManager->flush();
    }

    /**
     * Return the Member of a Player in an Alliance, if found
     *
     * @param Player   $player
     * @param Alliance $alliance
     * @return Member|null
     */
    public function findMember(Player $player, Alliance $alliance): ?Member
    {
        return $this->memberRepository->findOneBy([
            'alliance' => $alliance,
            'player'   => $player,
        ]);
    }
}