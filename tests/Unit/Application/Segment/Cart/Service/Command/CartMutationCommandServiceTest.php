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

use App\Core\Application\Segment\Cart\Service\Command\CartMutationCommandService;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Cart\Service\Command\CartControlCommandContract,
    Segment\Cart\Service\Command\CartItemCommandContract,
    Segment\Cart\Service\Command\CartMutationCommandContract,
    Segment\Cart\Service\Query\CartControlQueryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Cart\Service\Command\CartMutationCommandService
*/
final class CartMutationCommandServiceTest extends TestCase
{
    private SecurityPolicyContract&MockObject $securityPolicy;
    private CartControlQueryContract&MockObject $cartControlQuery;
    private CartControlCommandContract&MockObject $cartControlCommand;
    private CartItemCommandContract&MockObject $cartItemCommand;
    private CartMutationCommandService $service;
    private User&MockObject $user;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->user = $this->createMock(User::class);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(CartMutationCommandContract::class, $this->service);
    }

    public function testAddToCartCreatesCartWhenUserHasNone(): void
    {
        $this->setupVerifiedUser();
        $this->cartControlQuery->method('getUserCart')->willReturn(null);
        $this->cartItemCommand->method('addProductToCart')->willReturn(['success' => true]);

        $this->cartControlCommand
            ->expects($this->once())
            ->method('createNewCart')
            ->with($this->user);

        $this->service->addToCart(1);
    }

    public function testAddToCartDoesNotCreateCartWhenUserAlreadyHasOne(): void
    {
        $this->setupVerifiedUser();
        $this->withExistingCart();
        $this->cartItemCommand->method('addProductToCart')->willReturn(['success' => true]);

        $this->cartControlCommand
            ->expects($this->never())
            ->method('createNewCart');

        $this->service->addToCart(1);
    }

    public function testAddToCartCallsAddProductToCartWithCorrectVariantId(): void
    {
        $this->setupVerifiedUser();
        $this->withExistingCart();

        $this->cartItemCommand
            ->expects($this->once())
            ->method('addProductToCart')
            ->with($this->user, 42)
            ->willReturn(['success' => true]);

        $this->service->addToCart(42);
    }

    public function testAddToCartReturnsResultFromCartItemCommand(): void
    {
        $expected = ['success' => true, 'message' => 'Product added to cart.'];

        $this->setupVerifiedUser();
        $this->withExistingCart();
        $this->cartItemCommand->method('addProductToCart')->willReturn($expected);

        $result = $this->service->addToCart(1);

        $this->assertSame($expected, $result);
    }

    public function testRemoveFromCartDoesNotCreateCart(): void
    {
        $this->setupVerifiedUser();
        $this->cartItemCommand->method('removeProductFromCart')->willReturn(['success' => true]);

        $this->cartControlCommand
            ->expects($this->never())
            ->method('createNewCart');

        $this->cartControlQuery
            ->expects($this->never())
            ->method('getUserCart');

        $this->service->removeFromCart(5);
    }

    public function testRemoveFromCartCallsRemoveProductFromCartWithCorrectItemId(): void
    {
        $this->setupVerifiedUser();

        $this->cartItemCommand
            ->expects($this->once())
            ->method('removeProductFromCart')
            ->with($this->user, 99)
            ->willReturn(['success' => true]);

        $this->service->removeFromCart(99);
    }

    public function testRemoveFromCartReturnsResultFromCartItemCommand(): void
    {
        $expected = ['success' => true, 'message' => 'Product removed from cart.'];

        $this->setupVerifiedUser();
        $this->cartItemCommand->method('removeProductFromCart')->willReturn($expected);

        $result = $this->service->removeFromCart(5);

        $this->assertSame($expected, $result);
    }

    public function testAddToCartChecksEmailVerification(): void
    {
        $this->withExistingCart();
        $this->cartItemCommand->method('addProductToCart')->willReturn([]);

        $this->securityPolicy
            ->expects($this->once())
            ->method('checkIfEmailVerified')
            ->willReturn($this->user);

        $this->service->addToCart(1);
    }

    public function testRemoveFromCartChecksEmailVerification(): void
    {
        $this->cartItemCommand->method('removeProductFromCart')->willReturn([]);

        $this->securityPolicy
            ->expects($this->once())
            ->method('checkIfEmailVerified')
            ->willReturn($this->user);

        $this->service->removeFromCart(1);
    }

    private function initMocks(): void
    {
        $this->securityPolicy = $this->createMock(SecurityPolicyContract::class);
        $this->cartControlQuery = $this->createMock(CartControlQueryContract::class);
        $this->cartControlCommand = $this->createMock(CartControlCommandContract::class);
        $this->cartItemCommand = $this->createMock(CartItemCommandContract::class);
    }

    private function initService(): void
    {
        $this->service = new CartMutationCommandService(
            $this->securityPolicy,
            $this->cartControlQuery,
            $this->cartControlCommand,
            $this->cartItemCommand,
        );
    }

    private function setupVerifiedUser(): void
    {
        $this->securityPolicy
            ->method('checkIfEmailVerified')
            ->willReturn($this->user);
    }

    private function withExistingCart(): void
    {
        $this->cartControlQuery
            ->method('getUserCart')
            ->willReturn($this->createMock(Cart::class));
    }
}
