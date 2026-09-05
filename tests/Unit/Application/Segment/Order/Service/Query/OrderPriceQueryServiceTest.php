<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Service\Query;

use PHPUnit\Framework\TestCase;

use App\Core\Domain\Segment\Order\ValueObject\Stripe\{
    StripeLineItemObject,
    StripeLineItemPriceObject
};

use App\Core\Application\Segment\Order\Service\Query\OrderPriceQueryService;

use App\Core\Ports\Segment\Order\Service\Query\OrderPriceQueryContract;

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Query\OrderPriceQueryService
*/
final class OrderPriceQueryServiceTest extends TestCase
{
    private OrderPriceQueryService $service;

    protected function setUp(): void
    {
        $this->service = new OrderPriceQueryService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderPriceQueryContract::class, $this->service);
    }

    public function testCalculatesTotalPriceReturnsZeroForEmptyItems(): void
    {
        $result = $this->service->calculateTotalPrice([]);

        $this->assertSame(0.0, $result);
    }

    public function testCalculatesTotalPriceForSingleItem(): void
    {
        $item = $this->buildLineItem(unitAmount: 1000, quantity: 1);

        $result = $this->service->calculateTotalPrice([$item]);

        $this->assertSame(10.0, $result);
    }

    public function testCalculatesTotalPriceConvertsUnitAmountFromCents(): void
    {
        $item = $this->buildLineItem(unitAmount: 999, quantity: 1);

        $result = $this->service->calculateTotalPrice([$item]);

        $this->assertSame(9.99, $result);
    }

    public function testCalculatesTotalPriceMultipliesQuantity(): void
    {
        $item = $this->buildLineItem(unitAmount: 1000, quantity: 3);

        $result = $this->service->calculateTotalPrice([$item]);

        $this->assertSame(30.0, $result);
    }

    public function testCalculatesTotalPriceAccumulatesAcrossMultipleItems(): void
    {
        $items = [
            $this->buildLineItem(unitAmount: 1000, quantity: 2),
            $this->buildLineItem(unitAmount: 500, quantity: 1),
        ];

        $result = $this->service->calculateTotalPrice($items);

        $this->assertSame(25.0, $result);
    }

    public function testCalculatesTotalPriceForMultipleQuantities(): void
    {
        $items = [
            $this->buildLineItem(unitAmount: 2000, quantity: 2),
            $this->buildLineItem(unitAmount: 1500, quantity: 3),
        ];

        $result = $this->service->calculateTotalPrice($items);

        $this->assertSame(85.0, $result);
    }

    private function buildLineItem(int $unitAmount, int $quantity): StripeLineItemObject
    {
        $price = new StripeLineItemPriceObject(
            currency:    'eur',
            productName: 'Test Product',
            unitAmount:  $unitAmount,
        );

        return new StripeLineItemObject(price: $price, quantity: $quantity);
    }
}
