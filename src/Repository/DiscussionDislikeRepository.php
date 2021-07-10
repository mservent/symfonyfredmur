<?php

namespace App\Repository;

use App\Entity\DiscussionDislike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DiscussionDislike|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiscussionDislike|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiscussionDislike[]    findAll()
 * @method DiscussionDislike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionDislikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscussionDislike::class);
    }

    // /**
    //  * @return DiscussionDislike[] Returns an array of DiscussionDislike objects
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
    public function findOneBySomeField($value): ?DiscussionDislike
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
