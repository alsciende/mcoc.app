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
        // get a player and make her create an alliance
        $player1 = self::createPlayer();
        $alliance = $this->service->createAlliance($player1, 'ttas1', __METHOD__);
        $this->assertInstanceOf(Alliance::class, $alliance);
        // the player is Leader of the alliance
        $member1 = $this->service->findMember($player1, $alliance);
        $this->assertInstanceOf(Member::class, $member1);
        $this->assertEquals($member1, $player1->getMember());
        $this->assertEquals(Member::ROLE_LEADER, $member1->getRole());
        // the alliance has 1 member
        $this->assertCount(1, $this->service->listMembers($alliance));
        // the leader cannot be demoted
        $this->expectException(CannotDemoteLeaderException::class);
        $this->service->demoteOfficer($member1);
        // the leader cannot be promoted
        $this->expectException(CannotPromoteLeaderException::class);
        $this->service->promoteMember($member1);
        // the leader cannot leave the alliance
        $this->expectException(LeaderCannotLeaveException::class);
        $this->service->removeMember($member1);
        // get a second player and make her apply to the alliance
        $player2 = self::createPlayer();
        $this->service->applyToAlliance($player2, $alliance);
        // the alliance has 1 pending candidate
        $listCandidates = $this->service->listCandidates($alliance);
        $this->assertCount(1, $listCandidates);
        $candidate = $listCandidates[0];
        $this->assertInstanceOf(Candidate::class, $candidate);
        $this->assertEquals($alliance, $candidate->getAlliance());
        $this->assertEquals($player2, $candidate->getPlayer());
        $this->assertFalse($candidate->getIsRejected());
        // accept the candidate
        $this->service->acceptCandidate($candidate);
        $listCandidates = $this->service->listCandidates($alliance);
        $this->assertCount(0, $listCandidates);
        // the new member is a regular member
        $member2 = $this->service->findMember($player2, $alliance);
        $this->assertInstanceOf(Member::class, $member2);
        $this->assertEquals($alliance, $member2->getAlliance());
        $this->assertEquals($player2, $member2->getPlayer());
        $this->assertEquals(Member::ROLE_MEMBER, $member2->getRole());
        // the alliance has 2 members
        $this->assertCount(2, $this->service->listMembers($alliance));
        // the alliance cannot be destroyed because it is not empty
        $this->expectException(CannotDestroyNonemptyAllianceException::class);
        $this->service->destroyAlliance($alliance);
        // promote new member
        $this->service->promoteMember($member2);
        $this->assertEquals(Member::ROLE_OFFICER, $member2->getRole());
        // transfer leadership to new member
        $this->service->transferLeadership($member2);
        $this->assertEquals(Member::ROLE_LEADER, $member2->getRole());
        $this->assertEquals(Member::ROLE_OFFICER, $member1->getRole());
        // demote former leader
        $this->service->demoteOfficer($member1);
        $this->assertEquals(Member::ROLE_MEMBER, $member1->getRole());
        // kick former leader
        $this->service->removeMember($member1);
        $this->assertNull($this->service->findMember($player1, $alliance));
        $this->assertNull($player1->getMember());
        // alliance has 1 member
        $this->assertCount(1, $this->service->listMembers($alliance));
        // create third player, apply to alliance
        $player3 = self::createPlayer();
        $candidate = $this->service->applyToAlliance($player3, $alliance);
        $this->assertInstanceOf(Candidate::class, $candidate);
        // reject candidate
        $this->service->rejectCandidate($candidate);
        $this->assertCount(0, $this->service->listCandidates($alliance));
        $this->assertCount(1, $this->service->listCandidates($alliance, true));
        // alliance can be destroyed
        $this->service->destroyAlliance($alliance);
    }
}