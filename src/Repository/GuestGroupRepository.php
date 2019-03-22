<?php

namespace App\Repository;

use App\Entity\GuestGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GuestGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuestGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuestGroup[]    findAll()
 * @method GuestGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuestGroup::class);
    }

    /**
     * @return GuestGroup[] Returns an array of GuestGroup objects
     */
    public function findAllQueryBuilder()
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g', 'mgg')
            ->leftJoin('g.mailGuestGroups', 'mgg')
            ->leftJoin('g.people', 'p')
            ->addSelect('p.attendance')
            ;
    
        return $qb->getQuery()->getArrayResult();
    }

    // /**
    //  * @return GuestGroup[] Returns an array of GuestGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GuestGroup
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
