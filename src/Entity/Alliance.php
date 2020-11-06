<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * This is an in-game alliance of players
 *
 * @ORM\Entity(repositoryClass=\App\Repository\AllianceRepository::class)
 */
class Alliance
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $tag;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Member::class, mappedBy="alliance")
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity=Candidate::class, mappedBy="alliance")
     */
    private $candidates;

    /**
     * @ORM\OneToMany(targetEntity=Battlegroup::class, mappedBy="alliance")
     */
    private $battlegroups;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->members = new ArrayCollection();
        $this->candidates = new ArrayCollection();
        $this->battlegroups = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $member->setAlliance($this);
        }

        return $this;
    }

    public function removeMember(Member $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
            // set the owning side to null (unless already changed)
            if ($member->getAlliance() === $this) {
                $member->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Candidate[]
     */
    public function getCandidates(): Collection
    {
        return $this->candidates;
    }

    public function addCandidate(Candidate $candidate): self
    {
        if (!$this->candidates->contains($candidate)) {
            $this->candidates[] = $candidate;
            $candidate->setAlliance($this);
        }

        return $this;
    }

    public function removeCandidate(Candidate $candidate): self
    {
        if ($this->candidates->contains($candidate)) {
            $this->candidates->removeElement($candidate);
            // set the owning side to null (unless already changed)
            if ($candidate->getAlliance() === $this) {
                $candidate->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Battlegroup[]
     */
    public function getBattlegroups(): Collection
    {
        return $this->battlegroups;
    }

    public function addBattlegroup(Battlegroup $battlegroup): self
    {
        if (!$this->battlegroups->contains($battlegroup)) {
            $this->battlegroups[] = $battlegroup;
            $battlegroup->setAlliance($this);
        }

        return $this;
    }

    public function removeBattlegroup(Battlegroup $battlegroup): self
    {
        if ($this->battlegroups->removeElement($battlegroup)) {
            // set the owning side to null (unless already changed)
            if ($battlegroup->getAlliance() === $this) {
                $battlegroup->setAlliance(null);
            }
        }

        return $this;
    }
}
