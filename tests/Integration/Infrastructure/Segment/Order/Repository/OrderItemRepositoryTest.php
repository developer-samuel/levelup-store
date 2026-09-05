<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Segment\Order\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Core\Domain\{
    Segment\Order\Entity\OrderItem,
    Segment\Order\Enum\OrderStatus
};

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Segment\Order\Repository\OrderItemRepositoryContract;

use App\Infrastructure\Segment\Order\Repository\OrderItemRepository;

use Tests\{
    Support\Factory\OrderFactory,
    Support\Factory\ProductVariantFactory,
    Support\Factory\UserFactory,
    Support\Provides\Persistence
};

/**
 * @coversDefaultClass \App\Infrastructure\Segment\Order\Repository\OrderItemRepository
*/
final class OrderItemRepositoryTest extends KernelTestCase
{
    use Persistence;
    use UserFactory;
    use OrderFactory;
    use ProductVariantFactory;

    private EntityManagerInterface $em;
    private OrderItemRepository $repository;
    private User $user;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = $this->getEntityManager();
        $this->repository = $this->getRepository();

        $this->em->beginTransaction();

        $this->user = $this->createAndPersistUser('test@example.com');
    }

    protected function tearDown(): void
    {
        $this->em->rollback();

        parent::tearDown();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderItemRepositoryContract::class, $this->repository);
    }

    public function testFindByOrderReturnsItemsForOrder(): void
    {
        $order = $this->createAndPersistOrder($this->user, 'ORDER-ITEM-001');
        $variant = $this->createAndPersistVariant('SKU-ITEM-001', 'Variant Item Test', 'variant-item-test');
        $ean = $this->createAndPersistEan($variant, '1234567890123');

        $this->createAndPersistOrderItem($order, $variant, $ean);

        $result = $this->repository->findByOrder($order);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(OrderItem::class, $result[0]);
    }

    public function testFindByOrderReturnsEmptyWhenNoItems(): void
    {
        $order = $this->createAndPersistOrder($this->user, 'ORDER-EMPTY-001');

        $result = $this->repository->findByOrder($order);

        $this->assertEmpty($result);
    }

    public function testFindByOrderReturnsOnlyItemsForGivenOrder(): void
    {
        $orderA = $this->createAndPersistOrder($this->user, 'ORDER-MULTI-A');
        $orderB = $this->createAndPersistOrder($this->user, 'ORDER-MULTI-B');

        $variantA = $this->createAndPersistVariant('SKU-MULTI-A', 'Variant Multi A', 'variant-multi-a');
        $variantB = $this->createAndPersistVariant('SKU-MULTI-B', 'Variant Multi B', 'variant-multi-b');
        $eanA = $this->createAndPersistEan($variantA, '1111111111111');
        $eanB = $this->createAndPersistEan($variantB, '2222222222222');

        $this->createAndPersistOrderItem($orderA, $variantA, $eanA);
        $this->createAndPersistOrderItem($orderB, $variantB, $eanB);

        $result = $this->repository->findByOrder($orderA);

        $this->assertCount(1, $result);
        $this->assertSame($eanA->getId(), $result[0]->getEan()->getId());
    }

    public function testHasPurchasedVariantReturnsTrueWhenPurchased(): void
    {
        $order = $this->createAndPersistOrder($this->user, 'ORDER-PURCH-001', OrderStatus::COMPLETED);
        $variant = $this->createAndPersistVariant('SKU-PURCH-001', 'Variant Purchased', 'variant-purchased');
        $ean = $this->createAndPersistEan($variant, '3333333333333');

        $this->createAndPersistOrderItem($order, $variant, $ean, price: 49.99);

        $result = $this->repository->hasPurchasedVariant($this->user, $variant->getId());

        $this->assertTrue($result);
    }

    public function testHasPurchasedVariantReturnsFalseWhenOrderNotCompleted(): void
    {
        $order = $this->createAndPersistOrder($this->user, 'ORDER-PEND-001', OrderStatus::PROCESSED);
        $variant = $this->createAndPersistVariant('SKU-PEND-001', 'Variant Pending', 'variant-pending');
        $ean = $this->createAndPersistEan($variant, '4444444444444');

        $this->createAndPersistOrderItem($order, $variant, $ean);

        $result = $this->repository->hasPurchasedVariant($this->user, $variant->getId());

        $this->assertFalse($result);
    }

    public function testHasPurchasedVariantReturnsFalseForDifferentUser(): void
    {
        $userA = $this->createAndPersistUser('1-test@example.com');
        $userB = $this->createAndPersistUser('2-test@example.com');
        $order = $this->createAndPersistOrder($userA, 'ORDER-DIFF-001', OrderStatus::COMPLETED);
        $variant = $this->createAndPersistVariant('SKU-DIFF-001', 'Variant Diff', 'variant-diff');
        $ean = $this->createAndPersistEan($variant, '5555555555555');

        $this->createAndPersistOrderItem($order, $variant, $ean);

        $result = $this->repository->hasPurchasedVariant($userB, $variant->getId());

        $this->assertFalse($result);
    }

    private function getRepository(): OrderItemRepository
    {
        $repository = static::getContainer()->get(OrderItemRepository::class);
        assert($repository instanceof OrderItemRepository);

        return $repository;
    }
}
