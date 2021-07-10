<?php

namespace App\Repository;

use App\Entity\Discussionlike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Discussionlike|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discussionlike|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discussionlike[]    findAll()
 * @method Discussionlike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionlikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussionlike::class);
    }

    // /**
    //  * @return Discussionlike[] Returns an array of Discussionlike objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Discussionlike
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
