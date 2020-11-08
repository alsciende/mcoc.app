<?php

namespace Scenario;

use App\Entity\Champion;
use App\Entity\Character;
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

    protected static function createCharacter(): Character
    {
        $character = new Character();
        $character->setId(Uuid::v4());
        $character->setName(Uuid::v4());
        $character->setType(Character::TYPE_COSMIC);

        self::getDoctrine()->getManagerForClass(Character::class)->persist($character);

        return $character;
    }

    protected static function createChampion(Character $character = null, int $tier = 6): Champion
    {
        if ($character === null) {
            $character = self::createCharacter();
        }

        $champion = new Champion();
        $champion->setId(Uuid::v4());
        $champion->setCharacter($character);
        $champion->setTier($tier);

        self::getDoctrine()->getManagerForClass(Champion::class)->persist($champion);

        return $champion;
    }

    protected static function createRoster()
    {
        
    }
}