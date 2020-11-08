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
        $player1 = self::createPlayer();
        $alliance = $this->allianceService->createAlliance($player1, '2', __METHOD__);

        $this->assertNotEmpty($alliance->getBattlegroups());
        $bg = $alliance->getBattlegroups()[0];
        $this->assertInstanceOf(Battlegroup::class, $bg);

        $this->service->assignMember($bg, $player1->getMember());
        $this->assertCount(1, $this->service->listMembers($bg));

        $champion1 = self::createChampion();
        $roster1 = $this->playerService->addChampion($player1, $champion1, 1, 0);

        $defender1 = $this->service->addDefender($bg, $roster1);
        $this->assertInstanceOf(Defender::class, $defender1);

        $this->service->assignNode($defender1, 40);
        $this->assertEquals(40, $defender1->getNode());

        $this->assertEquals(1, $this->service->getDiversity($bg));

        $this->assertCount(1, $this->service->listDefenders($bg));

        $player2 = self::createPlayer();
        $roster2 = $this->playerService->addChampion($player2, $champion1, 2, 200);

        $candidate = $this->allianceService->applyToAlliance($player2, $alliance);
        $this->allianceService->acceptCandidate($candidate);

        $this->service->assignMember($bg, $player2->getMember());
        $this->assertCount(2, $this->service->listMembers($bg));
        $this->assertCount(2, $this->service->listPotentialDefenders($bg));

        $defender2 = $this->service->addDefender($bg, $roster2);
        $this->assertInstanceOf(Defender::class, $defender2);

        $this->assertEquals(1, $this->service->getDiversity($bg));
        $this->assertCount(2, $this->service->listDefenders($bg));

        $champion3 = self::createChampion();
        $roster3 = $this->playerService->addChampion($player2, $champion3, 1, 0);
        $defender3 = $this->service->addDefender($bg, $roster3);
        $this->assertEquals(2, $this->service->getDiversity($bg));
        $this->assertCount(3, $this->service->listDefenders($bg));

        $this->service->removeDefender($defender2);
        $this->assertEquals(2, $this->service->getDiversity($bg));
        $this->assertCount(2, $this->service->listDefenders($bg));
    }
}