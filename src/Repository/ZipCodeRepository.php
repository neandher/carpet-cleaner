<?php

namespace App\Repository;

use App\Entity\ZipCode;
use App\Util\Pagination;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ZipCodeRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ZipCode::class);
    }

    public function queryLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $qb = $this->createQueryBuilder('zipCode');

        if (isset($routeParams['search'])) {
            $qb->andWhere('zipCode.description like :search')->setParameter('search', '%' . $routeParams['search'] . '%');
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

    public function queryLatestForm()
    {
        return $this->createQueryBuilder('zipCode')
            ->orderBy('zipCode.description', 'ASC');
    }

    /**
     * @param $zipCode
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneyCustomByDescription($zipCode)
    {
        return $this->createQueryBuilder('zip_code')
            ->where('zip_code.description = :zipcode')->setParameter('zipcode', $zipCode)
            ->andWhere('zip_code.isEnabled = 1')
            ->getQuery()->getOneOrNullResult();
    }
}
