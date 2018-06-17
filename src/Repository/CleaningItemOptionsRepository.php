<?php

namespace App\Repository;

use App\Entity\CleaningItemOptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CleaningItemOptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method CleaningItemOptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method CleaningItemOptions[]    findAll()
 * @method CleaningItemOptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CleaningItemOptionsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CleaningItemOptions::class);
    }

//    /**
//     * @return CleaningItemOptions[] Returns an array of CleaningItemOptions objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CleaningItemOptions
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
