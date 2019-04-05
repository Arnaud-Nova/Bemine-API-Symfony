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
            ->orderBy('rt.id', 'ASC')
            ->getQuery()
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
            ;
    
        return $qb->getArrayResult();
    }

    /**
     * 
     */
    public function findTableGuestsId($userWedding, $nameTable)
    {
        $qb = $this->createQueryBuilder('rt')
            ->select('rt.id')
            ->leftJoin('rt.wedding', 'w')
            ->where('w.id = :userWedding')
            ->andWhere('rt.name = :nameTable')
            ->setParameter('userWedding', $userWedding)
            ->setParameter('nameTable', $nameTable)
            ->getQuery()
            ;
    
        return $qb->getArrayResult();
    }

    
    /**
     * 
     */
    public function findByWeddingTheTableGuests($userWedding, $nameTable)
    {
        $qb = $this->createQueryBuilder('rt')
            ->select('rt')
            ->leftJoin('rt.wedding', 'w')
            ->where('w.id = :userWedding')
            ->andWhere('rt.name = :nameTable')
            ->setParameter('userWedding', $userWedding)
            ->setParameter('nameTable', $nameTable)
            ->getQuery()
            ;
    
        return $qb->getArrayResult();
    }
}
