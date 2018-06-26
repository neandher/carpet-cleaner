<?php

namespace App\Repository;

use App\Entity\ScheduleItems;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ScheduleItems|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScheduleItems|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScheduleItems[]    findAll()
 * @method ScheduleItems[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleItemsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ScheduleItems::class);
    }

//    /**
//     * @return ScheduleItems[] Returns an array of ScheduleItems objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ScheduleItems
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
