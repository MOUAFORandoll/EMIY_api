<?php

namespace App\Repository;

use App\Entity\ListProduitPanier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListProduitPanier>
 *
 * @method ListProduitPanier|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListProduitPanier|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListProduitPanier[]    findAll()
 * @method ListProduitPanier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListProduitPanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListProduitPanier::class);
    }

    public function add(ListProduitPanier $entity, bool $flush = false): void
    {
        $this->serializerEntityManager()->persist($entity);

        if ($flush) {
            $this->serializerEntityManager()->flush();
        }
    }

    public function remove(ListProduitPanier $entity, bool $flush = false): void
    {
        $this->serializerEntityManager()->remove($entity);

        if ($flush) {
            $this->serializerEntityManager()->flush();
        }
    }

    //    /**
    //     * @return ListProduitPanier[] Returns an array of ListProduitPanier objects
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

    //    public function findOneBySomeField($value): ?ListProduitPanier
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
