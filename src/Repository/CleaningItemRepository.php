<?php

namespace App\Repository;

use App\Entity\CleaningItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CleaningItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CleaningItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CleaningItem[]    findAll()
 * @method CleaningItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CleaningItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CleaningItem::class);
    }

//    /**
//     * @return CleaningItem[] Returns an array of CleaningItem objects
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
    public function findOneBySomeField($value): ?CleaningItem
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
