<?php

namespace App\Service;

use App\Entity\Champion;
use App\Entity\Player;
use App\Entity\Roster;
use App\Exception\DuplicatedRosterException;
use App\Repository\PlayerRepository;
use App\Repository\RosterRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Management of a Player and their Champions
 */
class PlayerService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var PlayerRepository
     */
    private PlayerRepository $playerRepository;

    /**
     * @var RosterRepository
     */
    private RosterRepository $rosterRepository;

    /**
     * @var ChallengerRatingCalculator
     */
    private ChallengerRatingCalculator $challengerRatingCalculator;

    /**
     * PlayerService constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PlayerRepository $playerRepository,
        RosterRepository $rosterRepository,
        ChallengerRatingCalculator $challengerRatingCalculator
    ) {
        $this->entityManager = $entityManager;
        $this->playerRepository = $playerRepository;
        $this->rosterRepository = $rosterRepository;
        $this->challengerRatingCalculator = $challengerRatingCalculator;
    }

    /**
     * List all Champions (optionally filtered by tier) and for each one, the optional Roster for this Player
     *
     * @param Player   $player
     * @param int|null $tier
     * @return array<string, array{'champion': Champion, 'roster': Roster|null}>
     */
    public function listChampionsAndRosters(Player $player, int $tier = null): array
    {
        $queryBuilder = $this
            ->entityManager
            ->createQueryBuilder()
            ->from(Champion::class, 'c')
            ->select('c')
            ->addSelect('r')
            ->join('c.character', 'x')
            ->leftJoin('App:Roster', 'r', Join::WITH, 'r.champion = c AND r.player = :player')
            ->setParameter('player', $player)
            ->orderBy('x.name', 'ASC');

        if ($tier !== null) {
            $queryBuilder->andWhere('c.tier = :tier')->setParameter('tier', $tier);
        }

        $result = $queryBuilder->getQuery()->getResult();

        $list = [];

        while (count($result)) {
            /** @var Champion $champion */
            /** @var Roster|null $roster */
            [$champion, $roster] = array_splice($result, 0, 2);

            $list[$champion->getId()] = [
                'champion' => $champion,
                'roster'   => $roster,
            ];
        }

        return $list;
    }

    /**
     * @param Roster $roster
     */
    public function removeChampion(Roster $roster): void
    {
        $this->entityManager->remove($roster);

        $this->entityManager->flush();
    }

    /**
     * @param Roster $roster
     * @param int    $rank
     * @param int    $signature
     */
    public function updateChampion(Roster $roster, int $rank, int $signature): void
    {
        $roster->setRank($rank);
        $roster->setSignature($signature);

        $this->entityManager->flush();
    }

    /**
     * @param Player   $player
     * @param Champion $champion
     * @param int      $rank
     * @param int      $signature
     * @return Roster
     */
    public function addChampion(Player $player, Champion $champion, int $rank, int $signature): Roster
    {
        $roster = new Roster();
        $roster->setPlayer($player);
        $roster->setChampion($champion);
        $roster->setRank($rank);
        $roster->setSignature($signature);
        $roster->setRating($this->challengerRatingCalculator->getChallengerRating($roster));

        $this->entityManager->persist($roster);
        try {
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new DuplicatedRosterException();
        }
        $this->entityManager->refresh($roster);

        return $roster;
    }

    /**
     * @param Player   $player
     * @param Champion $champion
     * @return Roster|null
     */
    public function getRoster(Player $player, Champion $champion): ?Roster
    {
        return $this->rosterRepository->findOneBy(['player' => $player, 'champion' => $champion]);
    }

    /**
     * @param Player $player
     * @return Roster[]
     */
    public function listRosters(Player $player): array
    {
        return $this->rosterRepository->findBy(['player' => $player]);
    }
}