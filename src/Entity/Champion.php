<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * This is a playable champion that you can get in a crystal, with a tier (number of stars)
 *
 * @ORM\Entity(repositoryClass=\App\Repository\ChampionRepository::class)
 */
class Champion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $tier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity="Character", fetch="EAGER")
     */
    private $character;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTier(): ?int
    {
        return $this->tier;
    }

    public function setTier(int $tier): self
    {
        $this->tier = $tier;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCharacter(): Character
    {
        return $this->character;
    }

    public function setCharacter($character): self
    {
        $this->character = $character;

        return $this;
    }
}
