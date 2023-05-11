<?php

namespace App\Repository;

use App\Entity\ListProduitPromotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListProduitPromotion>
 *
 * @method ListProduitPromotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListProduitPromotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListProduitPromotion[]    findAll()
 * @method ListProduitPromotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListProduitPromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListProduitPromotion::class);
    }

    public function add(ListProduitPromotion $entity, bool $flush = false): void
    {
        $this->serializerEntityManager()->persist($entity);

        if ($flush) {
            $this->serializerEntityManager()->flush();
        }
    }

    public function remove(ListProduitPromotion $entity, bool $flush = false): void
    {
        $this->serializerEntityManager()->remove($entity);

        if ($flush) {
            $this->serializerEntityManager()->flush();
        }
    }

    //    /**
    //     * @return ListProduitPromotion[] Returns an array of ListProduitPromotion objects
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

    //    public function findOneBySomeField($value): ?ListProduitPromotion
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
