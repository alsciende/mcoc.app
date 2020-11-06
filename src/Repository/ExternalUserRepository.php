<?php

namespace App\Repository;

use App\Entity\ExternalUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExternalUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalUser[]    findAll()
 * @method ExternalUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalUser::class);
    }

    // /**
    //  * @return ExternalUser[] Returns an array of ExternalUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExternalUser
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
