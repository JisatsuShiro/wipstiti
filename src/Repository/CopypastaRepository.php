<?php

namespace App\Repository;

use App\Entity\Copypasta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Copypasta>
 */
class CopypastaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Copypasta::class);
    }


    public function findTopThreeByCount(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.count', 'DESC') // Trier par count de manière décroissante
            ->setMaxResults(3)          // Limiter à 3 résultats
            ->getQuery()
            ->getResult();
    }
    
    //    /**
    //     * @return Copypasta[] Returns an array of Copypasta objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Copypasta
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
