<?php

namespace App\Repository;

use App\Entity\UserReadShort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserReadShort>
 *
 * @method UserReadShort|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserReadShort|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserReadShort[]    findAll()
 * @method UserReadShort[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserReadShortRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserReadShort::class);
    }

//    /**
//     * @return UserReadShort[] Returns an array of UserReadShort objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserReadShort
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
