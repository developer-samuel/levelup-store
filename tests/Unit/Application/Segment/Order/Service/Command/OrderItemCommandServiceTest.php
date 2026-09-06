<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Service\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderItem,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Enum\Variant\ProductVariantEanStatus
};

use App\Core\Application\Segment\Order\Service\Command\OrderItemCommandService;

use App\Core\Ports\{
    Segment\Cart\Service\Command\CartItemCommandContract,
    Segment\Order\Service\Command\OrderItemCommandContract,
    Segment\Order\Service\Query\OrderItemQueryContract,
    Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

use Tests\Support\Provides\AssertsPersisted;

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Command\OrderItemCommandService
*/
final class OrderItemCommandServiceTest extends TestCase
{
    use AssertsPersisted;

    private EntityPersistenceContract&MockObject $entityPersistence;
    private ProductVariantEanRepositoryContract&MockObject $eanRepository;
    private OrderItemQueryContract&MockObject $orderItemQuery;
    private CartItemCommandContract&MockObject $cartItemCommand;
    private OrderItemCommandService $service;
    private Order&MockObject $order;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->order = $this->createMock(Order::class);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderItemCommandContract::class, $this->service);
    }

    public function testProcessOrderItemsThrowsWhenVariantHasNoStock(): void
    {
        $variant = $this->buildVariantMock(stock: null);
        $cartItem = $this->buildCartItemMock($variant);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/stock/i');

        $this->service->processOrderItems($this->order, [$cartItem]);
    }

    public function testProcessOrderItemsThrowsAndRemovesCartItemsWhenOutOfStock(): void
    {
        $stock = $this->createMock(ProductVariantStock::class);
        $variant = $this->buildVariantMock(stock: $stock);
        $cartItem = $this->buildCartItemMock($variant);

        $this->orderItemQuery->method('isStockAvailable')->willReturn(false);
        $this->eanRepository->method('findAvailableByVariant')->willReturn([]);

        $this->cartItemCommand
            ->expects($this->once())
            ->method('removeVariant')
            ->with($variant);

        $this->expectException(\RuntimeException::class);

        $this->service->processOrderItems($this->order, [$cartItem]);
    }

    public function testProcessOrderItemsThrowsWhenNoEansAvailable(): void
    {
        $stock = $this->createMock(ProductVariantStock::class);
        $variant = $this->buildVariantMock(stock: $stock);
        $cartItem = $this->buildCartItemMock($variant);

        $this->orderItemQuery->method('isStockAvailable')->willReturn(true);
        $this->eanRepository->method('findAvailableByVariant')->willReturn([]);

        $this->expectException(\RuntimeException::class);

        $this->service->processOrderItems($this->order, [$cartItem]);
    }

    public function testProcessOrderItemsThrowsWhenInsufficientEans(): void
    {
        $stock = $this->createMock(ProductVariantStock::class);
        $variant = $this->buildVariantMock(stock: $stock);

        $cartItems = [
            $this->buildCartItemMock($variant),
            $this->buildCartItemMock($variant),
        ];

        $this->orderItemQuery->method('isStockAvailable')->willReturn(true);
        $this->eanRepository->method('findAvailableByVariant')->willReturn([
            $this->createMock(ProductVariantEan::class),
        ]);

        $this->expectException(\RuntimeException::class);

        $this->service->processOrderItems($this->order, $cartItems);
    }

    public function testProcessOrderItemsReservesEansWithReservedStatus(): void
    {
        $ean = $this->createMock(ProductVariantEan::class);
        $ean->expects($this->once())
            ->method('setStatus')
            ->with(ProductVariantEanStatus::RESERVED);

        [$cartItem] = $this->buildValidCartItemWithStock();

        $this->withAvailableEan($ean);

        $this->service->processOrderItems($this->order, [$cartItem]);
    }

    public function testProcessOrderItemsPersistsOrderItem(): void
    {
        [$cartItem] = $this->buildValidCartItemWithStock();
        $this->withAvailableEan();

        $persisted = $this->capturePersistedOnProcess([$cartItem]);

        $this->assertPersistedContains($persisted, OrderItem::class);
    }

    public function testProcessOrderItemsUpdatesStockQuantity(): void
    {
        $stock = $this->createMock(ProductVariantStock::class);
        $stock->expects($this->once())->method('reserveQuantity')->with(1);

        $variant = $this->buildVariantMock(stock: $stock);
        $cartItem = $this->buildCartItemMock($variant);

        $this->withAvailableEan();

        $this->service->processOrderItems($this->order, [$cartItem]);
    }

    public function testProcessOrderItemsThrowsWhenStockUnavailableEvenWithAvailableEans(): void
    {
        $stock = $this->createMock(ProductVariantStock::class);
        $variant = $this->buildVariantMock(stock: $stock);
        $cartItem = $this->buildCartItemMock($variant);

        $this->orderItemQuery->method('isStockAvailable')->willReturn(false);
        $this->eanRepository->method('findAvailableByVariant')->willReturn([
            $this->createMock(ProductVariantEan::class),
        ]);

        $this->expectException(\RuntimeException::class);

        $this->service->processOrderItems($this->order, [$cartItem]);
    }

    public function testProcessOrderItemsPersistsEanEntity(): void
    {
        [$cartItem] = $this->buildValidCartItemWithStock();
        $this->withAvailableEan();

        $persisted = $this->capturePersistedOnProcess([$cartItem]);

        $this->assertPersistedContains($persisted, ProductVariantEan::class);
    }

    public function testProcessOrderItemsAggregatesItemsForSameVariant(): void
    {
        $stock = $this->createMock(ProductVariantStock::class);
        $stock->method('reserveQuantity');

        $variant = $this->buildVariantMock(stock: $stock);

        $cartItems = [
            $this->buildCartItemMock($variant),
            $this->buildCartItemMock($variant),
        ];

        $eans = [
            $this->createMock(ProductVariantEan::class),
            $this->createMock(ProductVariantEan::class),
        ];

        $this->eanRepository->method('findAvailableByVariant')->willReturn($eans);
        $this->orderItemQuery->method('isStockAvailable')->willReturn(true);

        $this->eanRepository
            ->expects($this->once())
            ->method('findAvailableByVariant')
            ->with($variant);

        $this->service->processOrderItems($this->order, $cartItems);
    }

    public function testValidateAllItemsInStockThrowsAndRemovesWhenVariantHasNoStock(): void
    {
        [$cartItem, $variant] = $this->buildCartItemWithoutStock();

        $this->cartItemCommand
            ->expects($this->once())
            ->method('removeVariant')
            ->with($variant);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/stock/i');

        $this->service->validateAllItemsInStock([$cartItem]);
    }

    public function testValidateAllItemsInStockThrowsAndRemovesWhenStockUnavailable(): void
    {
        [$cartItem, $variant] = $this->buildCartItemWithStockMock();

        $this->setupStockCheck(isAvailable: false, eans: []);

        $this->cartItemCommand
            ->expects($this->once())
            ->method('removeVariant')
            ->with($variant);

        $this->expectException(\RuntimeException::class);

        $this->service->validateAllItemsInStock([$cartItem]);
    }

    public function testValidateAllItemsInStockThrowsWhenNoEansAvailable(): void
    {
        [$cartItem] = $this->buildCartItemWithStockMock();

        $this->setupStockCheck(isAvailable: true, eans: []);

        $this->expectException(\RuntimeException::class);

        $this->service->validateAllItemsInStock([$cartItem]);
    }

    public function testValidateAllItemsInStockThrowsWhenCartQuantityExceedsAvailableEans(): void
    {
        [, $variant] = $this->buildCartItemWithStockMock();

        $cartItems = [
            $this->buildCartItemMock($variant),
            $this->buildCartItemMock($variant),
        ];

        $this->setupStockCheck(isAvailable: true, eans: [$this->createMock(ProductVariantEan::class)]);

        $this->expectException(\RuntimeException::class);

        $this->service->validateAllItemsInStock($cartItems);
    }

    public function testValidateAllItemsInStockPassesWhenStockIsAvailable(): void
    {
        [$cartItem] = $this->buildValidCartItemWithStock();
        $this->withAvailableEan();

        $this->expectNotToPerformAssertions();

        $this->service->validateAllItemsInStock([$cartItem]);
    }

    private function initMocks(): void
    {
        $this->entityPersistence = $this->createMock(EntityPersistenceContract::class);
        $this->eanRepository = $this->createMock(ProductVariantEanRepositoryContract::class);
        $this->orderItemQuery = $this->createMock(OrderItemQueryContract::class);
        $this->cartItemCommand = $this->createMock(CartItemCommandContract::class);
    }

    private function initService(): void
    {
        $this->service = new OrderItemCommandService(
            $this->entityPersistence,
            $this->eanRepository,
            $this->orderItemQuery,
            $this->cartItemCommand,
        );
    }

    private function buildVariantMock(?ProductVariantStock $stock): ProductVariant&MockObject
    {
        $variant = $this->createMock(ProductVariant::class);
        $variant->method('getId')->willReturn(1);
        $variant->method('getStock')->willReturn($stock);
        $variant->method('getDiscountedPrice')->willReturn(10.0);

        return $variant;
    }

    private function buildCartItemMock(ProductVariant $variant): CartItem&MockObject
    {
        $cartItem = $this->createMock(CartItem::class);
        $cartItem->method('getVariant')->willReturn($variant);

        return $cartItem;
    }

    /**
     * @return array{0: CartItem&MockObject, 1: ProductVariantStock&MockObject}
    */
    private function buildValidCartItemWithStock(): array
    {
        $stock = $this->createMock(ProductVariantStock::class);
        $stock->method('reserveQuantity');

        $variant = $this->buildVariantMock(stock: $stock);
        $cartItem = $this->buildCartItemMock($variant);

        return [$cartItem, $stock];
    }

    /**
     * @return array{0: CartItem&MockObject, 1: ProductVariant&MockObject}
    */
    private function buildCartItemWithoutStock(): array
    {
        $variant = $this->buildVariantMock(stock: null);
        $cartItem = $this->buildCartItemMock($variant);

        return [$cartItem, $variant];
    }

    /**
     * @return array{
     *     0: CartItem&MockObject,
     *     1: ProductVariant&MockObject,
     *     2: ProductVariantStock&MockObject
     * }
    */
    private function buildCartItemWithStockMock(): array
    {
        $stock = $this->createMock(ProductVariantStock::class);
        $variant = $this->buildVariantMock(stock: $stock);
        $cartItem = $this->buildCartItemMock($variant);

        return [$cartItem, $variant, $stock];
    }

    /**
     * @param ProductVariantEan[] $eans
    */
    private function setupStockCheck(bool $isAvailable, array $eans): void
    {
        $this->orderItemQuery->method('isStockAvailable')->willReturn($isAvailable);
        $this->eanRepository->method('findAvailableByVariant')->willReturn($eans);
    }

    private function withAvailableEan(?ProductVariantEan $ean = null): void
    {
        $this->eanRepository
            ->method('findAvailableByVariant')
            ->willReturn([$ean ?? $this->createMock(ProductVariantEan::class)]);

        $this->orderItemQuery->method('isStockAvailable')->willReturn(true);
    }

    /**
     * @param CartItem[] $cartItems
     *
     * @return object[]
    */
    private function capturePersistedOnProcess(array $cartItems): array
    {
        $persisted = [];

        $this->entityPersistence
            ->method('persist')
            ->willReturnCallback(function (object $entity) use (&$persisted): void {
                $persisted[] = $entity;
            });

        $this->service->processOrderItems($this->order, $cartItems);

        return $persisted;
    }
}
