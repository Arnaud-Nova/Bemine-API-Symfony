<?php

namespace App\Repository;

use App\Entity\ReceptionTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReceptionTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceptionTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceptionTable[]    findAll()
 * @method ReceptionTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceptionTableRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReceptionTable::class);
    }

    /**
     * 
     */
    public function findTablesByWedding($userWedding)
    {
        $qb = $this->createQueryBuilder('rt')
            ->select('rt', 'p')
            ->leftJoin('rt.wedding', 'w')
            ->leftJoin('rt.people', 'p')
            ->where('w.id = :userWedding')
            ->setParameter('userWedding', $userWedding)
            ->getQuery()
            // ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }

    /**
     * 
     */
    public function findOneTableById($id)
    {
        $qb = $this->createQueryBuilder('rt')
            ->select('rt', 'p')
            ->leftJoin('rt.people', 'p')
            ->where('rt.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            // ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }


    // /**
    //  * @return ReceptionTable[] Returns an array of ReceptionTable objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReceptionTable
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
