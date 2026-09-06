<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Repository;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    ORM\EntityManagerInterface,
    ORM\QueryBuilder,
    Persistence\ManagerRegistry
};

use Kit\Assertion\Domain\Product\Variant\ProductVariantAssertion;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderItem,
    Segment\Order\Enum\OrderStatus,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User
};

use App\Core\Ports\Segment\Order\Repository\OrderItemRepositoryContract;

use App\Infrastructure\Shared\Traits\SingleResult;

/**
 * @extends ServiceEntityRepository<OrderItem>
*/
final class OrderItemRepository extends ServiceEntityRepository implements OrderItemRepositoryContract
{
    use SingleResult;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ManagerRegistry $registry
    */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        ManagerRegistry $registry,
    ) {
        parent::__construct(
            $registry,
            OrderItem::class,
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderItem[]
     */
    public function findByOrder(Order $order): array
    {
        return $this->findBy(['order' => $order]);
    }

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return bool
    */
    public function hasPurchasedVariant(User $user, int $variantId): bool
    {
        $qb = $this->createVariantPurchaseQuery($user, $variantId);

        return $this->getScalarIntResult($qb) > 0;
    }

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return QueryBuilder
    */
    private function createVariantPurchaseQuery(User $user, int $variantId): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select('COUNT(oi.id)')
            ->from(OrderItem::class, 'oi')
            ->where('oi.variant = :variant')
            ->andWhere($this->getUserCompletedOrdersSubquery())
            ->setParameter('variant', $this->getVariantReference($variantId))
            ->setParameter('user', $user)
            ->setParameter('statuses', OrderStatus::completedStatuses());
    }

    /**
     * @return string
    */
    private function getUserCompletedOrdersSubquery(): string
    {
        return 'oi.order IN (
            SELECT o.id FROM ' . Order::class . ' o
            WHERE o.user = :user AND o.status IN (:statuses)
        )';
    }

    /**
     * @param int $variantId
     *
     * @return ProductVariant
    */
    private function getVariantReference(int $variantId): ProductVariant
    {
        $variant = $this->entityManager->getReference(ProductVariant::class, $variantId);
        ProductVariantAssertion::assertExists($variant);

        return $variant;
    }
}
