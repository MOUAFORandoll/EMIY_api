<?php

namespace App\Repository;

use App\Entity\ShortCommentLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShortCommentLike>
 *
 * @method ShortCommentLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortCommentLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortCommentLike[]    findAll()
 * @method ShortCommentLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortCommentLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortCommentLike::class);
    }

//    /**
//     * @return ShortCommentLike[] Returns an array of ShortCommentLike objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ShortCommentLike
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
