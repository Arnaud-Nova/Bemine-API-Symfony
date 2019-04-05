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
    public function findGroupsQueryBuilder($id)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g', 'p')
            ->leftJoin('g.people', 'p')
            ->where('p.wedding = :myId')
            ->setParameter('myId', $id)
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }

    /**
     * @return GuestGroup[] Returns an array of GuestGroup objects
     */
    public function findGroupAndContactPerson($id)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g.id as groupId', 'g.email as groupEmail', 'g.mailStatus', 'p.firstname', 'p.lastname')
            ->leftJoin('g.people', 'p')
            ->where('p.wedding = :myId')
            ->setParameter('myId', $id)
            ->andWhere('g.contactPerson = p.id')
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }

    /**
     * @return GuestGroup[] Returns an array of GuestGroup objects
     */
    public function findGroupInfosQueryBuilder($id)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g.id', 'g.email')
            ->leftJoin('g.people', 'p')
            ->where('p.wedding = :myId')
            ->setParameter('myId', $id)
            ->getQuery()
            ;
    
        return $qb->getArrayResult();
    }
    
    /**
     * @return GuestGroup[] Returns an array of GuestGroup objects
     */
    public function findByGuestGroupQueryBuilder($id)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g', 'p', 'mgg', 'w')
            ->leftJoin('g.people', 'p')
            ->leftJoin('g.mailGuestGroups', 'mgg')
            ->leftJoin('g.wedding', 'w')
            ->where('g.id = :myId')
            ->setParameter('myId', $id)
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }

     
    /**
     * @return GuestGroup[] Returns an array of GuestGroup objects
     */
    public function findByGuestGroupIdQueryBuilder($id)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g', 'p')
            ->leftJoin('g.people', 'p')
            ->where('g.id = :myId')
            ->setParameter('myId', $id)
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }

    /**
     * @return GuestGroup[] Returns an array of GuestGroup objects
     */
    public function findGuestGroupForWebsite($slugUrl)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g.id as groupId', 'g.email', 'IDENTITY (g.contactPerson) as contactPrincipal', 'p.id', 'p.firstname', 'p.lastname', 'p.attendance')
            ->leftJoin('g.people', 'p')
            ->where('g.slugUrl = :slugUrl')
            ->setParameter('slugUrl', $slugUrl)
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }

    /**
     * 
     */
    public function findThisWeddingBySlug($slugUrl)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('w.id')
            ->leftJoin('g.wedding', 'w')
            ->where('g.slugUrl = :slugUrl')
            ->setParameter('slugUrl', $slugUrl)
            ->getQuery()
            ;
    
        return $qb->getArrayResult();
    }
}
