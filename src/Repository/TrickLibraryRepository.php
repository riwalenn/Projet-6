<?php

namespace App\Repository;

use App\Entity\TrickLibrary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrickLibrary|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrickLibrary|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrickLibrary[]    findAll()
 * @method TrickLibrary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickLibraryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrickLibrary::class);
    }

    // /**
    //  * @return TrickLibrary[] Returns an array of TrickLibrary objects
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
    public function findOneBySomeField($value): ?TrickLibrary
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
