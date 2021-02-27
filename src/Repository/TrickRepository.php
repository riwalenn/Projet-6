<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function findAllWithLibraries($limit, $offset)
    {
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare("SELECT trick.*, trick_library.lien from trick 
                                                    LEFT JOIN trick_library ON trick.id = trick_library.trick_id 
                                                    and trick_library.id = (SELECT MIN(id) FROM trick_library 
                                                    WHERE type = 1 AND trick_library.trick_id = trick.id)
                                                    ORDER BY created_at DESC LIMIT :limit, :offset");
        $stmt->bindValue('limit', $limit, ParameterType::INTEGER);
        $stmt->bindValue('offset', $offset, ParameterType::INTEGER);
        $stmt->execute();
        return $stmt->fetchAllAssociative();
    }

    // /**
    //  * @return Trick[] Returns an array of Trick objects
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
    public function findOneBySomeField($value): ?Trick
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
