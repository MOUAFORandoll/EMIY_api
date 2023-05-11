<?php

namespace App\Repository;

use App\Entity\BoutiqueObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BoutiqueObject>
 *
 * @method BoutiqueObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoutiqueObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoutiqueObject[]    findAll()
 * @method BoutiqueObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoutiqueObjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoutiqueObject::class);
    }

    public function add(BoutiqueObject $entity, bool $flush = false): void
    {
        $this->serializerEntityManager()->persist($entity);

        if ($flush) {
            $this->serializerEntityManager()->flush();
        }
    }

    public function remove(BoutiqueObject $entity, bool $flush = false): void
    {
        $this->serializerEntityManager()->remove($entity);

        if ($flush) {
            $this->serializerEntityManager()->flush();
        }
    }

    //    /**
    //     * @return BoutiqueObject[] Returns an array of BoutiqueObject objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BoutiqueObject
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
