<?php

namespace App\Repository;

use App\Entity\ShortLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShortLike>
 *
 * @method ShortLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortLike[]    findAll()
 * @method ShortLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortLike::class);
    }

//    /**
//     * @return ShortLike[] Returns an array of ShortLike objects
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

//    public function findOneBySomeField($value): ?ShortLike
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
