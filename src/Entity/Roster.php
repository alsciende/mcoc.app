<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * This is a Champion in a Player's Collection
 *
 * @ORM\Entity(repositoryClass=\App\Repository\RosterRepository::class)
 */
class Roster
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="rosters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity=Champion::class, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $champion;

    /**
     * @ORM\Column(type="integer")
     */
    private $rank;

    /**
     * @ORM\Column(type="integer")
     */
    private $signature;

    /**
     * @ORM\OneToMany(targetEntity=Defender::class, mappedBy="roster")
     */
    private $defenders;

    /**
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * Roster constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->defenders = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getChampion(): ?Champion
    {
        return $this->champion;
    }

    public function setChampion(?Champion $champion): self
    {
        $this->champion = $champion;

        return $this;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getSignature(): ?int
    {
        return $this->signature;
    }

    public function setSignature(int $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return Collection|Defender[]
     */
    public function getDefenders(): Collection
    {
        return $this->defenders;
    }

    public function addDefender(Defender $defender): self
    {
        if (!$this->defenders->contains($defender)) {
            $this->defenders[] = $defender;
            $defender->setRoster($this);
        }

        return $this;
    }

    public function removeDefender(Defender $defender): self
    {
        if ($this->defenders->removeElement($defender)) {
            // set the owning side to null (unless already changed)
            if ($defender->getRoster() === $this) {
                $defender->setRoster(null);
            }
        }

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
