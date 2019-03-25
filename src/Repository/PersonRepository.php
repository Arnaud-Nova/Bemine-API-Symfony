<?php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Person|null find($id, $lockMode = null, $lockVersion = null)
 * @method Person|null findOneBy(array $criteria, array $orderBy = null)
 * @method Person[]    findAll()
 * @method Person[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Person::class);
    }

    /**
     * 
     */
    public function findTotalGuestsCountQueryBuilder($id)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.wedding = :myId')
            ->andWhere('p.newlyweds = 0')
            ->setParameter('myId', $id)
            ;
    
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * 
     */
    public function findAttendancePresentCountQueryBuilder($id)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.attendance)')
            ->where('p.attendance = 1')
            ->andWhere('p.newlyweds = 0')
            ->andWhere('p.wedding = :myId')
            ->setParameter('myId', $id)
            ;
    
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * 
     */
    public function findAttendanceAbsentCountQueryBuilder($id)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.attendance)')
            ->where('p.attendance = 0')
            ->andWhere('p.newlyweds = 0')
            ->andWhere('p.wedding = :myId')
            ->setParameter('myId', $id)
            ;
    
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * 
     */
    public function findAttendanceWaitingCountQueryBuilder($id)
    {
        //problème null n'est pas compté
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.attendance is NULL')
            ->andWhere('p.newlyweds = 0')
            ->andWhere('p.wedding = :myId')
            ->setParameter('myId', $id)
            ;
    
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * 
     */
    public function findAllQueryBuilder($id)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.id', 'p.firstname', 'p.lastname', 'p.attendance')
            ->where('p.wedding = :myId')
            ->andWhere('p.newlyweds = 0')
            ->setParameter('myId', $id)
            ;
    
        return $qb->getQuery()->getArrayResult();
    }

    // /**
    //  * @return Person[] Returns an array of Person objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Person
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
