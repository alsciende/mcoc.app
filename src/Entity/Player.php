<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * This is a player account
 *
 * @ORM\Entity(repositoryClass=\App\Repository\PlayerRepository::class)
 */
class Player
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="players")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Roster::class, mappedBy="player")
     */
    private $rosters;

    /**
     * @ORM\OneToOne(targetEntity=Member::class, mappedBy="player", fetch="EAGER")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;

    /**
     * @ORM\OneToMany(targetEntity=Candidate::class, mappedBy="player")
     */
    private $candidates;

    /**
     * Player constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->rosters = new ArrayCollection();
        $this->candidates = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Roster[]
     */
    public function getRosters(): Collection
    {
        return $this->rosters;
    }

    public function addRoster(Roster $roster): self
    {
        if (!$this->rosters->contains($roster)) {
            $this->rosters[] = $roster;
            $roster->setPlayer($this);
        }

        return $this;
    }

    public function removeRoster(Roster $roster): self
    {
        if ($this->rosters->contains($roster)) {
            $this->rosters->removeElement($roster);
            // set the owning side to null (unless already changed)
            if ($roster->getPlayer() === $this) {
                $roster->setPlayer(null);
            }
        }

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(Member $member = null): self
    {
        $this->member = $member;

        // set the owning side of the relation if necessary
        if ($member !== null && $member->getPlayer() !== $this) {
            $member->setPlayer($this);
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
            $candidate->setPlayer($this);
        }

        return $this;
    }

    public function removeCandidate(Candidate $candidate): self
    {
        if ($this->candidates->contains($candidate)) {
            $this->candidates->removeElement($candidate);
            // set the owning side to null (unless already changed)
            if ($candidate->getPlayer() === $this) {
                $candidate->setPlayer(null);
            }
        }

        return $this;
    }
}
