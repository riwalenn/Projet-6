<?php

namespace App\Repository;

use App\Entity\Trick;
use App\Entity\TrickLibrary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\ResultSetMapping;
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

    public function findTricksAndFirstMedia($limit, $offset)
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Trick::class, 't');
        $rsm->addFieldResult('t', 'id', 'id');
        $rsm->addFieldResult('t', 'title', 'title');
        $rsm->addFieldResult('t', 'slug', 'slug');
        $rsm->addFieldResult('t', 'created_at', 'created_at');
        $rsm->addJoinedEntityResult(TrickLibrary::class, 'tl', 't','trickLibraries');
        $rsm->addMetaResult('tl', 'lien', 'lien');

        $sql = 'SELECT trick.id, trick.title, trick.slug, trick.created_at, trick_library.lien from trick LEFT JOIN trick_library ON trick.id = trick_library.trick_id and trick_library.id = (SELECT MIN(id) FROM trick_library WHERE type = 1 AND trick_library.trick_id = trick.id) ORDER BY created_at DESC LIMIT ?, ?';
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $limit);
        $query->setParameter(2, $offset);
        return $query->getResult(AbstractQuery::HYDRATE_SCALAR);
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
