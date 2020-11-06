<?php

namespace App\Repository;

use App\Entity\Battlegroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Battlegroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Battlegroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Battlegroup[]    findAll()
 * @method Battlegroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BattlegroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Battlegroup::class);
    }

    // /**
    //  * @return Battlegroup[] Returns an array of Battlegroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Battlegroup
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
