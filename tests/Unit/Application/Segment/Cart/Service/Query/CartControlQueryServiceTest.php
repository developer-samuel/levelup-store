<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Cart\Service\Query;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\Segment\Cart\Entity\Cart;

use App\Core\Application\Segment\Cart\Service\Query\CartControlQueryService;

use Tests\Support\Stub\UserStub;

use App\Core\Ports\{
    Segment\Cart\Repository\CartRepositoryContract,
    Segment\Cart\Service\Query\CartControlQueryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Cart\Service\Query\CartControlQueryService
*/
final class CartControlQueryServiceTest extends TestCase
{
    use UserStub;

    private CartRepositoryContract&MockObject $cartRepository;
    private CartControlQueryService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(CartControlQueryContract::class, $this->service);
    }

    public function testGetUserCartReturnsCartWhenFound(): void
    {
        $user = $this->createUserWithId(1);
        $cart = $this->createMock(Cart::class);

        $this->cartRepository
            ->method('findCartForUser')
            ->with(1)
            ->willReturn($cart);

        $result = $this->service->getUserCart($user);

        $this->assertSame($cart, $result);
    }

    public function testGetUserCartReturnsNullWhenNotFound(): void
    {
        $user = $this->createUserWithId(2);

        $this->cartRepository
            ->method('findCartForUser')
            ->willReturn(null);

        $result = $this->service->getUserCart($user);

        $this->assertNull($result);
    }

    public function testGetUserCartCallsRepositoryWithUserId(): void
    {
        $user = $this->createUserWithId(42);

        $this->cartRepository
            ->expects($this->once())
            ->method('findCartForUser')
            ->with(42)
            ->willReturn(null);

        $this->service->getUserCart($user);
    }

    private function initMocks(): void
    {
        $this->cartRepository = $this->createMock(CartRepositoryContract::class);
    }

    private function initService(): void
    {
        $this->service = new CartControlQueryService($this->cartRepository);
    }
}
