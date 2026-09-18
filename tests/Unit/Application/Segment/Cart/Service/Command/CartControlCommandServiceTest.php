<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Cart\Service\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\User\Entity\User
};

use App\Core\Application\Segment\Cart\Service\Command\CartControlCommandService;

use App\Core\Ports\{
    Segment\Cart\Repository\CartItemRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Cart\Service\Command\CartControlCommandService
*/
final class CartControlCommandServiceTest extends TestCase
{
    private EntityPersistenceContract&MockObject $entityPersistence;
    private CartItemRepositoryContract&MockObject $cartItemRepository;
    private CartControlCommandService $service;
    private Cart&MockObject $cart;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->cart = $this->createMock(Cart::class);
    }

    public function testClearCartRemovesCartWhenNoItemsRemain(): void
    {
        $this->withEmptyCart();

        $this->entityPersistence
            ->expects(self::exactly(2))
            ->method('remove')
            ->with($this->cart, true);

        $this->service->clearCart($this->cart);
    }

    public function testClearCartRemovesCartWithItems(): void
    {
        $this->withCartItems();

        $this->entityPersistence
            ->expects(self::once())
            ->method('remove')
            ->with($this->cart, true);

        $this->service->clearCart($this->cart);
    }

    public function testFlushAndRefreshCartCallsFlushTwice(): void
    {
        $this->withCartItems();

        $this->entityPersistence
            ->expects(self::exactly(2))
            ->method('flush');

        $this->service->flushAndRefreshCart($this->cart);
    }

    public function testFlushAndRefreshCartCallsRefresh(): void
    {
        $this->withCartItems();

        $this->entityPersistence
            ->expects(self::once())
            ->method('refresh')
            ->with($this->cart);

        $this->service->flushAndRefreshCart($this->cart);
    }

    public function testFlushAndRefreshCartRemovesCartWhenNoItemsRemainAfterRefresh(): void
    {
        $this->withEmptyCart();

        $this->entityPersistence
            ->expects(self::once())
            ->method('remove')
            ->with($this->cart, true);

        $this->service->flushAndRefreshCart($this->cart);
    }

    private function initMocks(): void
    {
        $this->entityPersistence = $this->createMock(EntityPersistenceContract::class);
        $this->cartItemRepository = $this->createMock(CartItemRepositoryContract::class);
    }

    private function initService(): void
    {
        $this->service = new CartControlCommandService(
            $this->entityPersistence,
            $this->cartItemRepository,
        );
    }

    private function withEmptyCart(): void
    {
        $this->cartItemRepository->method('findByCart')->willReturn([]);
    }

    private function withCartItems(): void
    {
        $this->cartItemRepository->method('findByCart')->willReturn([new \stdClass()]);
    }
}
