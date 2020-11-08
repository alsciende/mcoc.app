<?php

namespace Scenario;

use App\Entity\Alliance;
use App\Entity\Candidate;
use App\Entity\Member;
use App\Exception\CannotDemoteLeaderException;
use App\Exception\CannotDestroyNonemptyAllianceException;
use App\Exception\CannotPromoteLeaderException;
use App\Exception\LeaderCannotLeaveException;
use App\Service\AllianceService;

class AllianceServiceTest extends AbstractScenarioTest
{
    private ?AllianceService $service;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->service = $kernel->getContainer()->get(AllianceService::class);
    }

    public function testAllianceScenario1()
    {
        $player1 = self::createPlayer();
        $alliance = $this->service->createAlliance($player1, 'ttas1', __METHOD__);
        $this->assertInstanceOf(Alliance::class, $alliance);

        $member1 = $this->service->findMember($player1, $alliance);
        $this->assertInstanceOf(Member::class, $member1);
        $this->assertEquals(Member::ROLE_LEADER, $member1->getRole());

        $this->assertCount(1, $this->service->listMembers($alliance));

        $this->expectException(CannotDemoteLeaderException::class);
        $this->service->demoteOfficer($member1);

        $this->expectException(CannotPromoteLeaderException::class);
        $this->service->promoteMember($member1);

        $this->expectException(LeaderCannotLeaveException::class);
        $this->service->removeMember($member1);

        $player2 = self::createPlayer();
        $this->service->applyToAlliance($player2, $alliance);

        $listCandidates = $this->service->listCandidates($alliance);
        $this->assertCount(1, $listCandidates);
        $candidate = $listCandidates[0];
        $this->assertInstanceOf(Candidate::class, $candidate);
        $this->assertEquals($alliance, $candidate->getAlliance());
        $this->assertEquals($player2, $candidate->getPlayer());
        $this->assertFalse($candidate->getIsRejected());

        $this->service->acceptCandidate($candidate);

        $listCandidates = $this->service->listCandidates($alliance);
        $this->assertCount(0, $listCandidates);

        $member2 = $this->service->findMember($player2, $alliance);
        $this->assertInstanceOf(Member::class, $member2);
        $this->assertEquals($alliance, $member2->getAlliance());
        $this->assertEquals($player2, $member2->getPlayer());
        $this->assertEquals(Member::ROLE_MEMBER, $member2->getRole());

        $this->assertCount(2, $this->service->listMembers($alliance));

        $this->expectException(CannotDestroyNonemptyAllianceException::class);
        $this->service->destroyAlliance($alliance);

        $this->service->promoteMember($member2);
        $this->assertEquals(Member::ROLE_OFFICER, $member2->getRole());

        $this->service->transferLeadership($member2);
        $this->assertEquals(Member::ROLE_LEADER, $member2->getRole());
        $this->assertEquals(Member::ROLE_OFFICER, $member1->getRole());

        $this->service->demoteOfficer($member1);
        $this->assertEquals(Member::ROLE_MEMBER, $member1->getRole());

        $this->service->removeMember($member1);
        $this->assertNull($this->service->findMember($player1, $alliance));

        $this->assertCount(1, $this->service->listMembers($alliance));

        $player3 = self::createPlayer();
        $candidate = $this->service->applyToAlliance($player3, $alliance);
        $this->assertInstanceOf(Candidate::class, $candidate);
        $this->service->rejectCandidate($candidate);
        $this->assertCount(0, $this->service->listCandidates($alliance));
        $this->assertCount(1, $this->service->listCandidates($alliance, true));

        $this->service->destroyAlliance($alliance);
    }
}