<?php

namespace App\Repository;

use App\Entity\ListCommandeLivreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListCommandeLivreur>
 *
 * @method ListCommandeLivreur|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListCommandeLivreur|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListCommandeLivreur[]    findAll()
 * @method ListCommandeLivreur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListCommandeLivreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListCommandeLivreur::class);
    }

    public function add(ListCommandeLivreur $entity, bool $flush = false): void
    {
        $this->serializerEntityManager()->persist($entity);

        if ($flush) {
            $this->serializerEntityManager()->flush();
        }
    }

    public function remove(ListCommandeLivreur $entity, bool $flush = false): void
    {
        $this->serializerEntityManager()->remove($entity);

        if ($flush) {
            $this->serializerEntityManager()->flush();
        }
    }

    //    /**
    //     * @return ListCommandeLivreur[] Returns an array of ListCommandeLivreur objects
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

    //    public function findOneBySomeField($value): ?ListCommandeLivreur
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
