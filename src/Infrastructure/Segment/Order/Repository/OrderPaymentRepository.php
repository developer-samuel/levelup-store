<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Repository;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    Persistence\ManagerRegistry
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderPayment
};

use App\Core\Ports\Segment\Order\Repository\OrderPaymentRepositoryContract;

/**
 * @extends ServiceEntityRepository<OrderPayment>
*/
final class OrderPaymentRepository extends ServiceEntityRepository implements OrderPaymentRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            OrderPayment::class,
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderPayment|null
    */
    public function getByOrder(Order $order): ?OrderPayment
    {
        return $this->findOneBy(['order' => $order]);
    }
}
