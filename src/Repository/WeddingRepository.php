<?php

namespace App\Repository;

use App\Entity\Wedding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Wedding|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wedding|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wedding[]    findAll()
 * @method Wedding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeddingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Wedding::class);
    }

    /**
     * 
     */
    public function findThisWedding($id)
    {
        $qb = $this->createQueryBuilder('w')
            ->select('w.id', 'w.date')
            ->where('w.id = :myId')
            ->setParameter('myId', $id)
            ->getQuery()
            // ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }
    // /**
    //  * @return Wedding[] Returns an array of Wedding objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Wedding
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
