<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=\App\Repository\DefenderRepository::class)
 */
class Defender
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Roster::class, inversedBy="defenders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $roster;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $node;

    /**
     * @ORM\ManyToOne(targetEntity=Battlegroup::class, inversedBy="defenders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $battlegroup;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getRoster(): ?Roster
    {
        return $this->roster;
    }

    public function setRoster(?Roster $roster): self
    {
        $this->roster = $roster;

        return $this;
    }

    public function getNode(): ?int
    {
        return $this->node;
    }

    public function setNode(?int $node): self
    {
        $this->node = $node;

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
