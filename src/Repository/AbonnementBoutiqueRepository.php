<?php

namespace App\Repository;

use App\Entity\AbonnementBoutique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AbonnementBoutique>
 *
 * @method AbonnementBoutique|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbonnementBoutique|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbonnementBoutique[]    findAll()
 * @method AbonnementBoutique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementBoutiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbonnementBoutique::class);
    }

    public function save(AbonnementBoutique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AbonnementBoutique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return AbonnementBoutique[] Returns an array of AbonnementBoutique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AbonnementBoutique
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
