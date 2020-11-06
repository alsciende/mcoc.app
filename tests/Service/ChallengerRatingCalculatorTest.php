<?php

namespace App\Tests\Service;

use App\Entity\Champion;
use App\Entity\Roster;
use App\Service\ChallengerRatingCalculator;
use PHPUnit\Framework\TestCase;

class ChallengerRatingCalculatorTest extends TestCase
{
    /**
     * @var ChallengerRatingCalculator
     */
    private ChallengerRatingCalculator $underTest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->underTest = new ChallengerRatingCalculator();
    }

    /**
     * @dataProvider rosterProvider
     */
    public function testCalculateRating(int $tier, int $rank, int $expectedRating)
    {
        $champion = new Champion();
        $champion->setTier($tier);
        $roster = new Roster();
        $roster->setChampion($champion);
        $roster->setRank($rank);

        $this->assertEquals($expectedRating, $this->underTest->getChallengerRating($roster));
    }

    public function rosterProvider(): array
    {
        return [
            [ 1, 1, 10 ],
            [ 1, 2, 20 ],
            [ 2, 1, 20 ],
            [ 2, 2, 30 ],
            [ 2, 3, 40 ],
            [ 3, 1, 40 ],
            [ 3, 2, 50 ],
            [ 3, 3, 60 ],
            [ 3, 4, 70 ],
            [ 4, 1, 60 ],
            [ 4, 2, 70 ],
            [ 4, 3, 80 ],
            [ 4, 4, 90 ],
            [ 4, 5, 100 ],
            [ 5, 1, 80 ],
            [ 5, 2, 90 ],
            [ 5, 3, 100 ],
            [ 5, 4, 110 ],
            [ 5, 5, 120 ],
            [ 6, 1, 110 ],
            [ 6, 2, 120 ],
            [ 6, 3, 130 ],
        ];
    }
}
