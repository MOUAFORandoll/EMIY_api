<?php

namespace App\Repository;

use App\Entity\NegociationProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NegociationProduit>
 *
 * @method NegociationProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method NegociationProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method NegociationProduit[]    findAll()
 * @method NegociationProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NegociationProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NegociationProduit::class);
    }

    public function save(NegociationProduit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NegociationProduit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return NegociationProduit[] Returns an array of NegociationProduit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NegociationProduit
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
