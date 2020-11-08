<?php

namespace App\Service;

use App\Entity\Battlegroup;
use App\Entity\Defender;
use App\Entity\Member;
use App\Entity\Roster;
use App\Exception\AllianceMismatchException;
use App\Repository\DefenderRepository;
use App\Repository\MemberRepository;
use App\Repository\RosterRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Actions related to a Battleground only
 */
class BattlegroupService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var RosterRepository
     */
    private RosterRepository $rosterRepository;

    /**
     * @var DefenderRepository
     */
    private DefenderRepository $defenderRepository;

    /**
     * @var MemberRepository
     */
    private MemberRepository $memberRepository;

    /**
     * BattlegroupService constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RosterRepository $rosterRepository,
        DefenderRepository $defenderRepository,
        MemberRepository $memberRepository
    ) {
        $this->entityManager = $entityManager;
        $this->rosterRepository = $rosterRepository;
        $this->defenderRepository = $defenderRepository;
        $this->memberRepository = $memberRepository;
    }

    /**
     * A Member can have Defenders in more than one Battlegroup, but his Battlegroup is the active one
     *
     * @param Battlegroup $battlegroup
     * @param Member      $member
     */
    public function assignMember(Battlegroup $battlegroup, Member $member): void
    {
        if ($battlegroup->getAlliance()->getId() !== $member->getAlliance()->getId()) {
            throw new AllianceMismatchException();
        }

        $member->setBattlegroup($battlegroup);
        $this->entityManager->flush();
    }

    /**
     * @param Battlegroup $battlegroup
     * @return Member[]
     */
    public function listMembers(Battlegroup $battlegroup): array
    {
        return $this->memberRepository->findBy(['battlegroup' => $battlegroup]);
    }

    /**
     * @param Battlegroup $battlegroup
     * @param Roster      $roster
     * @return Defender
     */
    public function addDefender(Battlegroup $battlegroup, Roster $roster): Defender
    {
        if ($battlegroup->getAlliance()->getId() !== $roster->getPlayer()->getMember()->getAlliance()->getId()) {
            throw new AllianceMismatchException();
        }

        $defender = new Defender();
        $defender->setRoster($roster);
        $defender->setBattlegroup($battlegroup);

        $this->entityManager->persist($defender);
        $this->entityManager->flush();
        $this->entityManager->refresh($defender);

        return $defender;
    }

    /**
     * @param Defender $defender
     */
    public function removeDefender(Defender $defender): void
    {
        $this->entityManager->remove($defender);
        $this->entityManager->flush();
    }

    /**
     * @param Defender $defender
     * @param int      $node
     */
    public function assignNode(Defender $defender, int $node): void
    {
        $defender->setNode($node);
        $this->entityManager->flush();
    }

    /**
     * @param Battlegroup $battlegroup
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDiversity(Battlegroup $battlegroup): int
    {
        $queryBuilder = $this
            ->entityManager
            ->createQueryBuilder()
            ->from(Defender::class, 'd')
            ->select('COUNT(DISTINCT x.id) as count')
            ->join('d.roster', 'r')
            ->join('r.champion', 'c')
            ->join('c.character', 'x')
            ->andWhere('d.battlegroup=:battlegroup')
            ->setParameter('battlegroup', $battlegroup);

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return intval($result['count']);
    }

    /**
     * @param Battlegroup $battlegroup
     * @return Defender[]
     */
    public function listDefenders(Battlegroup $battlegroup): array
    {
        return $this->defenderRepository->findBy(['battlegroup' => $battlegroup]);
    }

    /**
     * @param Battlegroup $battlegroup
     * @param int|null    $minRating
     * @param int|null    $maxRating
     * @return array
     */
    public function listPotentialDefenders(
        Battlegroup $battlegroup,
        int $minRating = null,
        int $maxRating = null
    ): array {
        return $this->rosterRepository->findByBattlegroupAndRating($battlegroup, $minRating, $maxRating);
    }
}