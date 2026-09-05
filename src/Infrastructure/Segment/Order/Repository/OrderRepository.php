<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Repository;

use Doctrine\{
    ORM\QueryBuilder,
    Persistence\ManagerRegistry
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Enum\OrderStatus,
    Segment\User\Entity\User
};

use App\Core\Ports\Segment\Order\Repository\OrderRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection,
    Shared\Traits\DateRange,
    Shared\Traits\OrderedQuery,
    Shared\Traits\SingleResult
};

/**
 * @extends AbstractRepository<Order>
*/
final class OrderRepository extends AbstractRepository implements OrderRepositoryContract
{
    use DateRange;
    use OrderedQuery;
    use SingleResult;

    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Order::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'o';
    }

    /**
     * @return string
    */
    protected function getFindAllSortColumn(): string
    {
        return 'createdAt';
    }

    /**
     * @return SortDirection
    */
    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::DESC;
    }

    /**
     * @param int $orderId
     *
     * @return Order|null
    */
    public function getOrder(int $orderId): ?Order
    {
        return $this->find($orderId);
    }

    /**
     * @param string $code
     *
     * @return Order|null
    */
    public function getOrderByCode(string $code): ?Order
    {
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return Order|null
    */
    public function findOne(array $criteria): ?Order
    {
        $qb = $this->createFindOneQueryBuilder($criteria);

        /** @var Order|null $order */
        $order = $this->getResultOrNull($qb);

        return $order instanceof Order ? $order : null;
    }

    /**
     * @param OrderStatus[] $statuses
     *
     * @return Order[]
    */
    public function findOrdersByStatuses(array $statuses): array
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('statuses', $this->getStatusValues($statuses));

        return $this->getOrdersFromQueryBuilder($qb);
    }

    /**
     * @param User $user
     *
     * @return Order[]
    */
    public function findAllForUser(User $user): array
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user);

        return $this->getOrdersFromQueryBuilder($qb);
    }

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return int
    */
    public function countOrdersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        return $this->countOrdersInRange($from, $to);
    }

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return int
    */
    public function countPaidOrdersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        return $this->countOrdersInRange($from, $to, OrderPaymentMethod::CARD, true);
    }

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return int
    */
    public function countUnpaidOrdersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        return $this->countOrdersInRange($from, $to, null, false);
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return QueryBuilder
    */
    private function createFindOneQueryBuilder(array $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('LOWER(o.code) = LOWER(:code)')
            ->setParameter('code', $criteria['code']);

        if (isset($criteria['user'])) {
            $qb->andWhere('o.user = :user')
                ->setParameter('user', $criteria['user']);
        }

        return $qb;
    }

    /**
     * @param OrderStatus[] $statuses
     *
     * @return string[]
    */
    private function getStatusValues(array $statuses): array
    {
        return array_map(
            fn(OrderStatus $status) => $status->value,
            $statuses,
        );
    }

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     *
     * @return Order[]
    */
    private function getOrdersFromQueryBuilder(QueryBuilder $qb, string $alias = 'o'): array
    {
        /** @var Order[] $results */
        $results = $this->getOrderedResults(
            $qb,
            $alias,
            Order::class,
            'createdAt',
            SortDirection::DESC,
        );

        return $results;
    }

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     * @param OrderPaymentMethod|null $paymentMethod
     * @param bool|null $paid
     *
     * @return int
    */
    private function countOrdersInRange(
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        ?OrderPaymentMethod $paymentMethod = null,
        ?bool $paid = null,
    ): int {
        $qb = $this->buildDateRangeQuery($from, $to, $paymentMethod)
            ->select('COUNT(o.id)');

        if ($paid) {
            $qb->innerJoin('o.orderPayment', 'op');
        }

        if (!$paid) {
            $qb->leftJoin('o.orderPayment', 'op')
                ->andWhere('op.id IS NULL');
        }

        return $this->getScalarIntResult($qb);
    }

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     * @param OrderPaymentMethod|null $paymentMethod
     *
     * @return QueryBuilder
    */
    private function buildDateRangeQuery(
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        ?OrderPaymentMethod $paymentMethod = null,
    ): QueryBuilder {
        $qb = $this->applyDateRange($this->createQueryBuilder('o'), 'o', $from, $to);

        if ($paymentMethod !== null) {
            $qb->andWhere('o.payment = :paymentMethod')
                ->setParameter('paymentMethod', $paymentMethod);
        }

        return $qb;
    }
}
