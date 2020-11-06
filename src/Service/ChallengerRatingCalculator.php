<?php

namespace App\Service;

use App\Entity\Champion;
use App\Entity\Roster;

class ChallengerRatingCalculator
{
    public function getChallengerRating(Roster $roster): int
    {
        $baseRating = $this->getBaseRating($roster->getChampion());

        return $baseRating + 10 * ($roster->getRank() - 1);
    }

    private function getBaseRating(Champion $champion): int
    {
        switch ($champion->getTier()) {
            case 1:
                return 10;
            case 2:
                return 20;
            case 3:
                return 40;
            case 4:
                return 60;
            case 5:
                return 80;
            case 6:
                return 110;
        }

        throw new \OutOfBoundsException();
    }
}