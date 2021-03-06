<?php

namespace App\Repository;

use App\Entity\PromotionCoupon;
use App\Util\Pagination;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PromotionCouponRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PromotionCoupon::class);
    }

    public function queryLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $qb = $this->createQueryBuilder('promotionCoupon');

        if (isset($routeParams['search'])) {
            $qb->andWhere('promotionCoupon.title like :search')->setParameter('search', '%' . $routeParams['search'] . '%');
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

    /**
     * @param $code
     * @param $amountSubTotal
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByCodeCustom($code, $amountSubTotal)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->where('p.code = :code')->setParameter('code', $code)
            ->andWhere('p.isEnabled = 1')
            ->andWhere(
                $qb->expr()->andX()->add(
                    $qb->expr()->orx()
                        ->add($qb->expr()->andX()
                            ->add($qb->expr()->isNotNull('p.expiresAt'))
                            ->add($qb->expr()->gte('p.expiresAt', ':now'))
                        )
                        ->add($qb->expr()->isNull('p.expiresAt'))
                )
            )
            ->andWhere(
                $qb->expr()->andX()->add(
                    $qb->expr()->orx()
                        ->add($qb->expr()->andX()
                            ->add($qb->expr()->isNotNull('p.usageLimit'))
                            ->add($qb->expr()->gt('p.usageLimit', 'IFNULL(p.used, 0)'))
                        )
                        ->add($qb->expr()->isNull('p.usageLimit'))
                        ->add($qb->expr()->eq('p.usageLimit', 0))
                )
            )
            ->andWhere(
                $qb->expr()->andX()->add(
                    $qb->expr()->orx()
                        ->add($qb->expr()->andX()
                            ->add($qb->expr()->isNotNull('p.initialAmount'))
                            ->add($qb->expr()->lte('p.initialAmount', ':amountSubTotal'))
                        )
                        ->add($qb->expr()->isNull('p.initialAmount'))
                )
            )
            ->setParameter('now', new \DateTime())
            ->setParameter('amountSubTotal', $amountSubTotal)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
