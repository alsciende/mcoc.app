<?php

namespace App\Repository;

use App\Entity\ExternalCharacter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExternalCharacter|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalCharacter|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalCharacter[]    findAll()
 * @method ExternalCharacter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalCharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalCharacter::class);
    }

    // /**
    //  * @return ExternalCharacter[] Returns an array of ExternalCharacter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExternalCharacter
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
