<?php

namespace App\Import\Hook;

class ChampionDataExtractor
{
    public function extractChampionData(string $content): array
    {
        if (false === preg_match_all('/typeId\(TYPE\.(\w+), \[\n(.*?)\n    \]\),/su', $content, $matches, PREG_SET_ORDER)) {
            throw new \RuntimeException((string) preg_last_error());
        }

        $data = [];
        foreach ($matches as $match) {
            $championsData = [];
            $champions = explode("\n", $match[2]);
            foreach ($champions as $champion) {
                [$name, $stars] = $this->extractStarData($champion);
                $championsData[$name] = $stars;
            }

            $data[strtolower($match[1])] = $championsData;
        }

        return $data;
    }

    private function extractStarData(string $content): array
    {
        if (false === preg_match('/championStars\({ uid: CHAMPION.(\w+) }, \[(.*)\]\)/', $content, $matches)) {
            throw new \RuntimeException((string) preg_last_error());
        }

        $name = trim($matches[1]);
        $stars = array_map(function ($token) {
            return intval(trim($token));
        }, explode(",", $matches[2]));

        return [ $name, $stars ];
    }

    public function extractIdData(string $content): array
    {
        if (false === preg_match_all('/export const (.*) = \'(.*)\';/u', $content, $matches, PREG_SET_ORDER)) {
            throw new \RuntimeException((string) preg_last_error());
        }

        $data = [];
        foreach ($matches as $match) {
            $data[$match[1]] = $match[2];
        }

        return $data;
    }
}