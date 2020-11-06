<?php

namespace App\Repository;

use App\Entity\Defender;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Defender|null find($id, $lockMode = null, $lockVersion = null)
 * @method Defender|null findOneBy(array $criteria, array $orderBy = null)
 * @method Defender[]    findAll()
 * @method Defender[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefenderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Defender::class);
    }
}
