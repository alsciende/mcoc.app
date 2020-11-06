<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * This is a character with everything in common between Champions of the same name
 *
 * @ORM\Entity(repositoryClass=\App\Repository\CharacterRepository::class)
 * @ORM\Table(name="`character`",uniqueConstraints={@ORM\UniqueConstraint(name="name_idx", columns={"name"})})
 */
class Character
{
    const TYPE_COSMIC = 'cosmic';
    const TYPE_TECH = 'tech';
    const TYPE_MUTANT = 'mutant';
    const TYPE_SKILL = 'skill';
    const TYPE_SCIENCE = 'science';
    const TYPE_MYSTIC = 'mystic';

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    public function __construct()
    {
        $this->id = Uuid::v4();
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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }
}
