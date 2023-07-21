<?php

namespace App\Repository;

use App\Entity\ShortComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShortComment>
 *
 * @method ShortComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortComment[]    findAll()
 * @method ShortComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortComment::class);
    }

//    /**
//     * @return ShortComment[] Returns an array of ShortComment objects
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

//    public function findOneBySomeField($value): ?ShortComment
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
