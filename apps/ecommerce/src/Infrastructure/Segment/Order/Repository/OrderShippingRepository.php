<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Repository;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    Persistence\ManagerRegistry
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderShipping
};

use App\Core\Ports\Segment\Order\Repository\OrderShippingRepositoryContract;

/** @extends ServiceEntityRepository<OrderShipping> */
final class OrderShippingRepository extends ServiceEntityRepository implements OrderShippingRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            OrderShipping::class,
        );
    }

    public function findOneByOrder(Order $order): ?OrderShipping
    {
        return $this->findOneBy(['order' => $order]);
    }
}
