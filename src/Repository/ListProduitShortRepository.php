<?php

namespace App\Repository;

use App\Entity\ListProduitShort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListProduitShort>
 *
 * @method ListProduitShort|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListProduitShort|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListProduitShort[]    findAll()
 * @method ListProduitShort[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListProduitShortRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListProduitShort::class);
    }

//    /**
//     * @return ListProduitShort[] Returns an array of ListProduitShort objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ListProduitShort
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
