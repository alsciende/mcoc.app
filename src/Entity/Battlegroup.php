<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=\App\Repository\BattlegroupRepository::class)
 */
class Battlegroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Alliance::class, inversedBy="battlegroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $alliance;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nickname;

    /**
     * @ORM\OneToMany(targetEntity=Member::class, mappedBy="battlegroup")
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity=Defender::class, mappedBy="battlegroup")
     */
    private $defenders;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->members = new ArrayCollection();
        $this->defenders = new ArrayCollection();
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * @return Collection|Member[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setBattlegroup($this);
        }

        return $this;
    }

    public function removeMember(Member $member): self
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getBattlegroup() === $this) {
                $member->setBattlegroup(null);
            }
        }

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
            $defender->setBattlegroup($this);
        }

        return $this;
    }

    public function removeDefender(Defender $defender): self
    {
        if ($this->defenders->removeElement($defender)) {
            // set the owning side to null (unless already changed)
            if ($defender->getBattlegroup() === $this) {
                $defender->setBattlegroup(null);
            }
        }

        return $this;
    }
}
