<?php

namespace Scenario;

use App\Entity\Battlegroup;
use App\Entity\Defender;
use App\Service\AllianceService;
use App\Service\BattlegroupService;
use App\Service\PlayerService;

class BattlegroupServiceTest extends AbstractScenarioTest
{
    private ?AllianceService $allianceService;

    private ?BattlegroupService $service;

    private ?PlayerService $playerService;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->allianceService = $kernel->getContainer()->get(AllianceService::class);
        $this->playerService = $kernel->getContainer()->get(PlayerService::class);
        $this->service = $kernel->getContainer()->get(BattlegroupService::class);
    }

    public function testBattlegroupScenario()
    {
        // get a player and an alliance
        $player1 = self::createPlayer();
        $alliance = $this->allianceService->createAlliance($player1, '2', __METHOD__);
        // alliance has battlegroups
        $this->assertNotEmpty($alliance->getBattlegroups());
        $bg = $alliance->getBattlegroups()[0];
        $this->assertInstanceOf(Battlegroup::class, $bg);
        // assign player to battlegroup
        $this->service->assignMember($bg, $player1->getMember());
        $this->assertCount(1, $this->service->listMembers($bg));
        // add a champion to player roster
        $champion1 = self::createChampion();
        $roster1 = $this->playerService->addChampion($player1, $champion1, 1, 0);
        // add that champion as a defender in the battlegroup
        $defender1 = $this->service->addDefender($bg, $roster1);
        $this->assertInstanceOf(Defender::class, $defender1);
        // assign the defender to a defense node
        $this->service->assignNode($defender1, 40);
        $this->assertEquals(40, $defender1->getNode());
        // calculate diversity
        $this->assertEquals(1, $this->service->getDiversity($bg));
        // list defenders in the battlegroup
        $this->assertCount(1, $this->service->listDefenders($bg));
        // create a second player with the same champion, different rank
        $player2 = self::createPlayer();
        $roster2 = $this->playerService->addChampion($player2, $champion1, 2, 200);
        // add that player to the alliance
        $candidate = $this->allianceService->applyToAlliance($player2, $alliance);
        $this->allianceService->acceptCandidate($candidate);
        // assign the second player to the same battlegroup
        $this->service->assignMember($bg, $player2->getMember());
        // check list of players and champions in the battlegroup
        $this->assertCount(2, $this->service->listMembers($bg));
        $this->assertCount(2, $this->service->listPotentialDefenders($bg));
        // add the champion of the second player as a defender
        $defender2 = $this->service->addDefender($bg, $roster2);
        $this->assertInstanceOf(Defender::class, $defender2);
        // check that diversity did not improve even as we have one more defender
        $this->assertEquals(1, $this->service->getDiversity($bg));
        $this->assertCount(2, $this->service->listDefenders($bg));
        // create a new champion, give it to the second player
        $champion2 = self::createChampion();
        $roster3 = $this->playerService->addChampion($player2, $champion2, 1, 0);
        // add that new champion as a defender in the battlegroup
        $defender3 = $this->service->addDefender($bg, $roster3);
        // check diversity did improve
        $this->assertEquals(2, $this->service->getDiversity($bg));
        $this->assertCount(3, $this->service->listDefenders($bg));
        // remove the duplicate champion from the second player
        $this->service->removeDefender($defender2);
        // check that diversity was not impacted
        $this->assertEquals(2, $this->service->getDiversity($bg));
        $this->assertCount(2, $this->service->listDefenders($bg));
    }
}