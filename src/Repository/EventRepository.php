<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * 
     */
    public function findThisWedding($id)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            // ->select('e', 'e.name as eventName')
            ->leftJoin('e.wedding', 'w')
            // ->leftJoin('we.event', 'e')
            ->where('w.id = :myId')
            ->setParameter('myId', $id)
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }

    /**
     * 
     */
    public function findEventsByWedding($id)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.id', 'e.name', 'e.active')
            // ->select('we', 'e.name as eventName')
            ->leftJoin('e.wedding', 'w')
            // ->leftJoin('we.event', 'e')
            ->where('w.id = :myId')
            ->setParameter('myId', $id)
            ->getQuery()
            // ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }

    /**
     * 
     */
    public function findEventsActiveByWedding($id)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.id as eventId', 'e.name as eventName', 'e.active as eventActive', 'e.address', 'e.postcode', 'e.city', 'e.schedule', 'e.hour', 'e.postcode', 'e.map')
            // ->select('we', 'e.name as eventName')
            ->leftJoin('e.wedding', 'w')
            // ->leftJoin('we.event', 'e')
            ->where('w.id = :myId')
            ->andWhere('e.active = 1')
            ->setParameter('myId', $id)
            ->getQuery()
            // ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
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
