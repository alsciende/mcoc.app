<?php

namespace Scenario;

use App\Entity\Roster;
use App\Exception\DuplicatedRosterException;
use App\Service\PlayerService;

class PlayerServiceTest extends AbstractScenarioTest
{

    private ?PlayerService $service;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->service = $kernel->getContainer()->get(PlayerService::class);
    }

    public function testPlayerScenario()
    {
        $player = self::createPlayer();
        $champion = self::createChampion();

        $this->service->addChampion($player, $champion, 1, 0);
        $this->expectException(DuplicatedRosterException::class);
        $this->service->addChampion($player, $champion, 2, 100);

        $roster = $this->service->getRoster($player, $champion);
        $this->assertInstanceOf(Roster::class, $roster);
        $this->assertEquals(1, $roster->getRank());
        $this->assertEquals(0, $roster->getSignature());

        $this->service->updateChampion($roster, 3, 200);
        $this->assertEquals(3, $roster->getRank());
        $this->assertEquals(200, $roster->getSignature());

        $this->assertCount(1, $this->service->listRosters($player));
        $this->service->removeChampion($roster);
        $this->assertCount(0, $this->service->listRosters($player));
    }
}