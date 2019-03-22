<?php

namespace App\Repository;

use App\Entity\MailGuestGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MailGuestGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method MailGuestGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method MailGuestGroup[]    findAll()
 * @method MailGuestGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailGuestGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MailGuestGroup::class);
    }

    // /**
    //  * @return MailGuestGroup[] Returns an array of MailGuestGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MailGuestGroup
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
