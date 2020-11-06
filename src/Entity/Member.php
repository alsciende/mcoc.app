<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * This is a Player in an Alliance
 *
 * @ORM\Entity(repositoryClass=\App\Repository\MemberRepository::class)
 */
class Member
{
    const ROLE_LEADER = 0;
    const ROLE_OFFICER = 1;
    const ROLE_MEMBER = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Alliance::class, inversedBy="members", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $alliance;

    /**
     * @ORM\OneToOne(targetEntity=Player::class, inversedBy="member")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\Column(type="integer")
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity=Battlegroup::class, inversedBy="members")
     */
    private $battlegroup;

    /**
     * Member constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): self
    {
        $this->alliance = $alliance;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }

    public function setRole(int $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getBattlegroup(): ?Battlegroup
    {
        return $this->battlegroup;
    }

    public function setBattlegroup(?Battlegroup $battlegroup): self
    {
        $this->battlegroup = $battlegroup;

        return $this;
    }
}
