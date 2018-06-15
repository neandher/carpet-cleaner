<?php

namespace App\Repository;

use App\Entity\CleaningItemOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CleaningItemOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method CleaningItemOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method CleaningItemOption[]    findAll()
 * @method CleaningItemOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CleaningItemOptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CleaningItemOption::class);
    }

//    /**
//     * @return CleaningItemOption[] Returns an array of CleaningItemOption objects
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
    public function findOneBySomeField($value): ?CleaningItemOption
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
