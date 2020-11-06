<?php

namespace App\Service;

use App\Entity\Player;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class SessionService
{
    /**
     * @var Security
     */
    private $security;

    /**
     * SessionService constructor.
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getActivePlayer(): Player
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $player = $user->getActivePlayer();

        if (false === $player instanceof Player) {
            throw new \LogicException("No active player");
        }

        return $player;
    }
}