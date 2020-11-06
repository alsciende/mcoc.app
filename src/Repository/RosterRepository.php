<?php

namespace App\Repository;

use App\Entity\Battlegroup;
use App\Entity\Roster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Roster|null find($id, $lockMode = null, $lockVersion = null)
 * @method Roster|null findOneBy(array $criteria, array $orderBy = null)
 * @method Roster[]    findAll()
 * @method Roster[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RosterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Roster::class);
    }

    /**
     * @return Roster[] Returns an array of Roster objects
     */
    public function findByChampionTier(UserInterface $user, int $tier)
    {
        return $this
            ->createQueryBuilder('r')
            ->join('r.champion', 'c')
            ->join('c.character', 'x')
            ->join('r.player', 'p')
            ->andWhere('c.tier = :tier')
            ->andWhere('p.user = :user')
            ->setParameter('tier', $tier)
            ->setParameter('user', $user)
            ->orderBy('x.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByBattlegroupAndRating(
        Battlegroup $battlegroup,
        int $minRating = null,
        int $maxRating = null
    ) {
        $queryBuilder = $this
            ->createQueryBuilder('r')
            ->join('r.champion', 'c')
            ->join('c.character', 'x')
            ->join('r.player', 'p')
            ->join('p.member', 'm')
            ->where('m.battlegroup=:battlegroup')
            ->setParameter('battlegroup', $battlegroup)
            ->addOrderBy('p.name', 'ASC')
            ->addOrderBy('r.rating', 'DESC')
            ->addOrderBy('x.name', 'ASC');

        if ($minRating !== null) {
            $queryBuilder->andWhere('r.rating >= :min')->setParameter('min', $minRating);
        }

        if ($maxRating !== null) {
            $queryBuilder->andWhere('r.rating <= :max')->setParameter('max', $maxRating);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
