<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
    * 
    */
    public function findUserProfilQueryBuilder($userId)
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id as userId','u.email')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }

    // /**
    // * 
    // */
    // public function findUserProfilQueryBuilder($userId)
    // {
    //     $qb = $this->createQueryBuilder('u')
    //         ->select('u.email', 'u.id as userId', 'w.id as weddingId', 'w.date as weddingDate', 'p.firstname', 'p.lastname')
    //         ->leftJoin('u.wedding', 'w')
    //         ->leftJoin('w.people', 'p')
    //         // ->innerJoin('')
    //         ->where('u.id = :userId')
    //         ->setParameter('userId', $userId)
    //         ->andWhere('p.newlyweds = 1')
    //         ->getQuery()
    //         ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
    //         ;
    
    //     return $qb->getArrayResult();
    // }
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
