<?php

namespace App\Tests\Scenario;

use App\Entity\Player;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

abstract class AbstractScenarioTest extends KernelTestCase
{
    protected static function getDoctrine(): Registry
    {
        $doctrine = self::$container->get('doctrine');

        if ($doctrine instanceof Registry) {
            return $doctrine;
        }

        throw new \LogicException(Registry::class . ' not available in test container.');
    }

    protected static function createUser(): User
    {
        $user = new User();
        $user->setName(Uuid::v4());

        self::getDoctrine()->getManagerForClass(User::class)->persist($user);

        return $user;
    }

    protected static function createPlayer(User $user = null, bool $setActive = true): Player
    {
        if ($user === null) {
            $user = self::createUser();
        }

        $player = new Player();
        $player->setName(Uuid::v4());
        $player->setUser($user);

        if ($setActive) {
            $user->setActivePlayer($player);
        }

        self::getDoctrine()->getManagerForClass(Player::class)->persist($player);

        return $player;
    }
}