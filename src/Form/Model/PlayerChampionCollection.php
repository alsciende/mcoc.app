<?php

namespace App\Form\Model;

class PlayerChampionCollection
{
    /** @var PlayerChampion[] */
    private $champions;

    /**
     * PlayerChampionCollection constructor.
     * @param PlayerChampion[] $champions
     */
    public function __construct(array $champions = [])
    {
        $this->champions = $champions;
    }

    /**
     * @return PlayerChampion[]|array
     */
    public function getChampions(): array
    {
        return $this->champions;
    }

    /**
     * @param PlayerChampion[] $champions
     * @return $this
     */
    public function setChampions(array $champions): self
    {
        $this->champions = $champions;

        return $this;
    }

    public function addChampion(PlayerChampion $playerChampion): self
    {
        $this->champions[] = $playerChampion;

        return $this;
    }
}