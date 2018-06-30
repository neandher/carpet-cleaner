<?php

namespace App\Repository;

use App\Entity\Schedule;
use App\Util\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use function Sodium\add;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ScheduleRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    public function queryLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $qb = $this->createQueryBuilder('schedule')
            ->innerJoin('schedule.customer', 'customer')
            ->addSelect('customer')
            ->leftJoin('schedule.promotionCoupon', 'promotionCoupon')
            ->addSelect('promotionCoupon');

        if (isset($routeParams['search']) && !empty($routeParams['search'])) {
            $qb->andWhere(
                $qb->expr()->like(
                    $qb->expr()->concat(
                        'customer.firstName',
                        $qb->expr()->concat($qb->expr()->literal(' '), 'customer.lastName')
                    ),
                    ':search'
                )
            )->setParameter('search', '%' . $routeParams['search'] . '%');
        }

        if (isset($routeParams['state']) && !empty($routeParams['state'])) {
            $qb->andWhere('schedule.state = :state')->setParameter('state', $routeParams['state']);
        }

        if ((isset($routeParams['start_date']) && !empty($routeParams['start_date'])) && (isset($routeParams['end_date']) && !empty($routeParams['end_date']))) {

            $startDate = \DateTime::createFromFormat('m/d/Y H:i', $routeParams['start_date'])->format('Y-m-d H:i');
            $endDate = \DateTime::createFromFormat('m/d/Y H:i', $routeParams['end_date'])->format('Y-m-d H:i');

            if ($startDate && $endDate) {
                $qb->andWhere($this->findByDateStartEndExpr($qb, $startDate, $endDate));
            }
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

    public function findByDateStartEnd($startDate, $endDate)
    {
        $qb = $this->createQueryBuilder('schedule');

        $dateStart = \DateTime::createFromFormat('m/d/Y H:i', $startDate)->format('Y-m-d H:i');
        $dateEnd = \DateTime::createFromFormat('m/d/Y H:i', $endDate)->format('Y-m-d H:i');

        return $qb
            ->where($this->findByDateStartEndExpr($qb, $dateStart, $dateEnd))
            ->getQuery()
            ->getResult();
    }

    private function findByDateStartEndExpr(QueryBuilder $qb, $startDate, $endDate)
    {
        $qb->setParameter('start_date', $startDate)
            ->setParameter('start_end', $endDate);

        return $qb->expr()->andX()->add(
            $qb->expr()->orX()
                ->add($qb->expr()->eq('schedule.startDateAt', ':start_date'))
                ->add($qb->expr()->eq('schedule.endDateAt', ':start_date'))
                ->add(
                    $qb->expr()->andX()
                        ->add($qb->expr()->gte('schedule.startDateAt', ':start_date'))
                        ->add($qb->expr()->lte('schedule.startDateAt', ':start_end'))
                )
                ->add(
                    $qb->expr()->andX()
                        ->add($qb->expr()->lte('schedule.startDateAt', ':start_date'))
                        ->add($qb->expr()->gte('schedule.endDateAt', ':start_date'))
                )
        );
    }

    public function findItemsById($id)
    {
        return $this->createQueryBuilder('schedule')
            ->innerJoin('schedule.scheduleItems', 'scheduleItems')
            ->addSelect('scheduleItems')
            ->innerJoin('scheduleItems.cleaningItem', 'cleaningItem')
            ->addSelect('cleaningItem')
            ->leftJoin('schedule.promotionCoupon', 'promotionCoupon')
            ->addSelect('promotionCoupon')
            ->where('schedule.id = :id')->setParameter(':id', $id)
            ->getQuery()
            ->getResult();
    }
}
