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
        // create player and champion
        $player = self::createPlayer();
        $champion = self::createChampion();
        // player cannot have same champion twice
        $this->service->addChampion($player, $champion, 1, 0);
        $this->expectException(DuplicatedRosterException::class);
        $this->service->addChampion($player, $champion, 2, 100);
        // roster is link between player and champion
        $roster = $this->service->getRoster($player, $champion);
        $this->assertInstanceOf(Roster::class, $roster);
        $this->assertEquals(1, $roster->getRank());
        $this->assertEquals(0, $roster->getSignature());
        // player has ranked up the champion
        $this->service->updateChampion($roster, 3, 200);
        $this->assertEquals(3, $roster->getRank());
        $this->assertEquals(200, $roster->getSignature());
        // player can remove the champion
        $this->assertCount(1, $this->service->listRosters($player));
        $this->service->removeChampion($roster);
        $this->assertCount(0, $this->service->listRosters($player));
    }
}