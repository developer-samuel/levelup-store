<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Repository;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    Persistence\ManagerRegistry
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderPersonal
};

use App\Core\Ports\Segment\Order\Repository\OrderPersonalRepositoryContract;

/**
 * @extends ServiceEntityRepository<OrderPersonal>
*/
final class OrderPersonalRepository extends ServiceEntityRepository implements OrderPersonalRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            OrderPersonal::class,
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderPersonal|null
    */
    public function findOneByOrder(Order $order): ?OrderPersonal
    {
        return $this->findOneBy(['order' => $order]);
    }
}
