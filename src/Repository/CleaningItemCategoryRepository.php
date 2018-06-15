<?php

namespace App\Repository;

use App\Entity\CleaningItemCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CleaningItemCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CleaningItemCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CleaningItemCategory[]    findAll()
 * @method CleaningItemCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CleaningItemCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CleaningItemCategory::class);
    }

//    /**
//     * @return CleaningItemCategory[] Returns an array of CleaningItemCategory objects
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
    public function findOneBySomeField($value): ?CleaningItemCategory
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
