<?php

namespace Import\Hook;

use App\Import\Hook\ChampionDataExtractor;
use PHPUnit\Framework\TestCase;

class ChampionDataExtractorTest extends TestCase
{
    public function testExtractChampionData()
    {
        $content = <<<HEREDOC
const champions = [
    ...typeId(TYPE.COSMIC, [
        ...championStars({ uid: CHAMPION.AIRWALKER }, [ 2, 3, 4, 5 ]),
        ...championStars({ uid: CHAMPION.VISIONAARKUS }, [ 2, 3, 5, 6 ]),
    ]),
    ...typeId(TYPE.TECH, [
        ...championStars({ uid: CHAMPION.CIVILWARRIOR }, [ 3, 4, 5, 6 ]),
    ]),
].map((champion) => new Champion(champion));
HEREDOC;

        $tested = new ChampionDataExtractor();

        $result = $tested->extractChampionData($content);

        $this->assertEquals([
            "cosmic" => [
                "AIRWALKER" => [ 2, 3, 4, 5 ],
                "VISIONAARKUS" => [ 2, 3, 5, 6 ],
            ],
            "tech" => [
                "CIVILWARRIOR" => [ 3, 4, 5, 6 ],
            ]
        ], $result);
    }

    public function testExtractIdData()
    {
        $content = <<<HEREDOC
export const AIRWALKER = 'airwalker';
export const ANGELA = 'angela';
HEREDOC;

        $tested = new ChampionDataExtractor();

        $result = $tested->extractIdData($content);

        $this->assertEquals([
            "AIRWALKER" => "airwalker",
            "ANGELA" => "angela"
        ], $result);
    }


}
