<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Repository;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    Persistence\ManagerRegistry
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling
};

use App\Core\Ports\Segment\Order\Repository\OrderBillingRepositoryContract;

/**
 * @extends ServiceEntityRepository<OrderBilling>
*/
final class OrderBillingRepository extends ServiceEntityRepository implements OrderBillingRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            OrderBilling::class,
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderBilling|null
    */
    public function findOneByOrder(Order $order): ?OrderBilling
    {
        return $this->findOneBy(['order' => $order]);
    }
}
