<?php

namespace App\Repository;

use App\Entity\Short;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Short>
 *
 * @method Short|null find($id, $lockMode = null, $lockVersion = null)
 * @method Short|null findOneBy(array $criteria, array $orderBy = null)
 * @method Short[]    findAll()
 * @method Short[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Short::class);
    }

    public function add(Short $entity, bool $flush = false): void
    {
        $this->serializerEntityManager()->persist($entity);

        if ($flush) {
            $this->serializerEntityManager()->flush();
        }
    }

    public function remove(Short $entity, bool $flush = false): void
    {
        $this->serializerEntityManager()->remove($entity);

        if ($flush) {
            $this->serializerEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Short[] Returns an array of Short objects
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

    //    public function findOneBySomeField($value): ?Short
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
