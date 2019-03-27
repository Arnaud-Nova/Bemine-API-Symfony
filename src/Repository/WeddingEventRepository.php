<?php

namespace App\Repository;

use App\Entity\WeddingEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WeddingEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method WeddingEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method WeddingEvent[]    findAll()
 * @method WeddingEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeddingEventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WeddingEvent::class);
    }

     /**
     * 
     */
    public function findThisWedding($id)
    {
        $qb = $this->createQueryBuilder('we')
            ->select('we')
            // ->select('we', 'e.name as eventName')
            ->leftJoin('we.wedding', 'w')
            // ->leftJoin('we.event', 'e')
            ->where('w.id = :myId')
            ->setParameter('myId', $id)
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }

    // /**
    //  * @return WeddingEvent[] Returns an array of WeddingEvent objects
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
    public function findOneBySomeField($value): ?WeddingEvent
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
