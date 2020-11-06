<?php

namespace App\Form\Model;

class PlayerChampion
{
    /** @var string */
    private $playerId;

    /** @var string */
    private $championId;

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var bool */
    private $checked;

    /** @var int|null */
    private $rank;

    /** @var int|null */
    private $signature;

    /**
     * PlayerChampion constructor.
     * @param string $playerId
     * @param string $championId
     * @param string $name
     * @param string $type
     */
    public function __construct(string $playerId, string $championId, string $name, string $type)
    {
        $this->playerId = $playerId;
        $this->championId = $championId;
        $this->name = $name;
        $this->type = $type;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    public function setPlayerId(string $playerId): self
    {
        $this->playerId = $playerId;

        return $this;
    }

    public function getChampionId(): string
    {
        return $this->championId;
    }

    public function setChampionId(string $championId): self
    {
        $this->championId = $championId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): self
    {
        $this->checked = $checked;

        return $this;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(?int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getSignature(): ?int
    {
        return $this->signature;
    }

    public function setSignature(?int $signature): self
    {
        $this->signature = $signature;

        return $this;
    }
}