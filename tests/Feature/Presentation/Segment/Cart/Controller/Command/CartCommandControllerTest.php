<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\Segment\Cart\Controller\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use Symfony\{
    Component\HttpFoundation\Request,
    Component\HttpFoundation\Response,
    Component\Security\Csrf\CsrfTokenManagerInterface
};

use App\Core\Ports\{
    Segment\Cart\Service\Command\CartMutationCommandContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\Segment\Cart\Controller\Command\CartCommandController;

/**
 * @coversDefaultClass \App\Presentation\Segment\Cart\Controller\Command\CartCommandController
*/
class CartCommandControllerTest extends TestCase
{
    private CsrfTokenManagerInterface&MockObject $csrfTokenManager;
    private CartMutationCommandContract&MockObject $cartMutationCommand;
    private AppLoggerContract&MockObject $logger;
    private CartCommandController $controller;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initController();
    }

    public function testStoreReturns200WhenAddToCartSucceeds(): void
    {
        $this->cartMutationCommand
            ->method('addToCart')
            ->willReturn(['success' => true, 'message' => 'Product added to cart.', 'html' => '', 'totalItems' => 1, 'totalPrice' => '10,00 €']);

        $request = $this->buildRequest(['variant_id' => 1]);

        $response = $this->controller->store($request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testStoreReturns422WhenVariantIdIsZero(): void
    {
        $request = $this->buildRequest(['variant_id' => 0]);

        $response = $this->controller->store($request);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testStoreReturns422WhenAddToCartReturnsFailure(): void
    {
        $this->cartMutationCommand
            ->method('addToCart')
            ->willReturn(['success' => false, 'message' => 'Out of stock.']);

        $request = $this->buildRequest(['variant_id' => 5]);

        $response = $this->controller->store($request);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testStoreCallsAddToCartWithVariantId(): void
    {
        $this->cartMutationCommand
            ->expects($this->once())
            ->method('addToCart')
            ->with(7)
            ->willReturn(['success' => true, 'message' => 'ok']);

        $request = $this->buildRequest(['variant_id' => 7]);

        $this->controller->store($request);
    }

    public function testStoreReturns500WhenExceptionThrown(): void
    {
        $this->cartMutationCommand
            ->method('addToCart')
            ->willThrowException(new \RuntimeException('fail'));

        $this->logger->expects($this->once())->method('logThrowable');

        $request = $this->buildRequest(['variant_id' => 1]);

        $response = $this->controller->store($request);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testDestroyReturns200WhenRemoveFromCartSucceeds(): void
    {
        $this->cartMutationCommand
            ->method('removeFromCart')
            ->willReturn(['success' => true, 'message' => 'Product removed from cart.']);

        $request = $this->buildRequest(['item_id' => 3]);

        $response = $this->controller->destroy($request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testDestroyReturns422WhenItemIdIsZero(): void
    {
        $request = $this->buildRequest(['item_id' => 0]);

        $response = $this->controller->destroy($request);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testDestroyCallsRemoveFromCartWithItemId(): void
    {
        $this->cartMutationCommand
            ->expects($this->once())
            ->method('removeFromCart')
            ->with(9)
            ->willReturn(['success' => true, 'message' => 'ok']);

        $request = $this->buildRequest(['item_id' => 9]);

        $this->controller->destroy($request);
    }

    public function testDestroyReturns422WhenRemoveFromCartReturnsFailure(): void
    {
        $this->cartMutationCommand
            ->method('removeFromCart')
            ->willReturn(['success' => false, 'message' => 'Error.']);

        $request = $this->buildRequest(['item_id' => 3]);

        $response = $this->controller->destroy($request);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testDestroyReturns500WhenExceptionThrown(): void
    {
        $this->cartMutationCommand
            ->method('removeFromCart')
            ->willThrowException(new \RuntimeException('fail'));

        $this->logger->expects($this->once())->method('logThrowable');

        $request = $this->buildRequest(['item_id' => 1]);

        $response = $this->controller->destroy($request);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @param array<string, mixed> $params
    */
    private function buildRequest(array $params): Request
    {
        return Request::create('/', 'POST', $params);
    }

    private function initMocks(): void
    {
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $this->cartMutationCommand = $this->createMock(CartMutationCommandContract::class);
        $this->logger = $this->createMock(AppLoggerContract::class);
    }

    private function initController(): void
    {
        $this->controller = new CartCommandController(
            $this->csrfTokenManager,
            $this->cartMutationCommand,
            $this->logger,
        );
    }
}
