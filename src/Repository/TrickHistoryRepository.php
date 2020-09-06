<?php

namespace App\Repository;

use App\Entity\TrickHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrickHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrickHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrickHistory[]    findAll()
 * @method TrickHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrickHistory::class);
    }

    // /**
    //  * @return TrickHistory[] Returns an array of TrickHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrickHistory
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
