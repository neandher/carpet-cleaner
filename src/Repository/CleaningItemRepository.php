<?php

namespace App\Repository;

use App\Entity\CleaningItem;
use App\Util\Pagination;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CleaningItemRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CleaningItem::class);
    }

    protected function queryLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $qb = $this->createQueryBuilder('cleaningItem')
            ->innerJoin('cleaningItem.cleaningItemCategory', 'cleaningItemCategory')
            ->addSelect('cleaningItemCategory');

        if (isset($routeParams['search'])) {
            $qb->andWhere('cleaningItem.title like :search')->setParameter('search', '%' . $routeParams['search'] . '%');
        }

        if (!empty($routeParams['category'])) {
            $qb->andWhere('cleaningItemCategory.id = :category')->setParameter('category', $routeParams['category']);
        }

        $qb = $this->addOrderingQueryBuilder($qb, $routeParams);

        return $qb->getQuery();
    }

    public function findLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryLatest($pagination), false));

        $paginator->setMaxPerPage($routeParams['num_items']);
        $paginator->setCurrentPage($routeParams['page']);

        return $paginator;
    }
}
