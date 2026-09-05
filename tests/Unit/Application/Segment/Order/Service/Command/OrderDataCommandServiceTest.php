<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Service\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Country\Entity\Country,
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling,
    Segment\Order\Entity\OrderPersonal,
    Segment\Order\Entity\OrderShipping,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject,
    Segment\Order\ValueObject\OrderPersonalObject
};

use App\Core\Application\{
    Segment\Order\Builder\OrderQueryBuilder,
    Segment\Order\Service\Command\OrderDataCommandService
};

use App\Core\Ports\{
    Segment\Country\CountryRepositoryContract,
    Segment\Order\Service\Command\OrderDataCommandContract,
    Segment\Order\Service\Query\OrderCacheQueryContract,
    Segment\Order\Service\Query\OrderCountryQueryContract,
    Segment\Order\Service\Query\OrderItemQueryContract,
    Segment\Order\Service\Query\OrderPaymentQueryContract,
    Segment\Order\Service\Query\OrderPreparationQueryContract,
    Segment\Order\Service\Query\OrderPriceQueryContract,
    Segment\Order\Service\Query\OrderValidatorQueryContract,
    Shared\Persistence\EntityPersistenceContract
};

use Tests\Support\Provides\AssertsPersisted;

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Command\OrderDataCommandService
*/
final class OrderDataCommandServiceTest extends TestCase
{
    use AssertsPersisted;

    private EntityPersistenceContract&MockObject $entityPersistence;
    private CountryRepositoryContract&MockObject $countryRepository;
    private OrderValidatorQueryContract&MockObject $orderValidatorQuery;
    private OrderCountryQueryContract&MockObject $orderCountryQuery;
    private OrderDataCommandService $service;
    private Order&MockObject $order;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->order = $this->createMock(Order::class);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderDataCommandContract::class, $this->service);
    }

    public function testAttachOrderDataPersistsPersonalData(): void
    {
        $this->withCountry();

        $this->assertPersistedContains(
            $this->attachAndCapturePersisted($this->buildPayload()),
            OrderPersonal::class,
        );
    }

    public function testAttachOrderDataPersistsBillingData(): void
    {
        $this->withCountry();

        $this->assertPersistedContains(
            $this->attachAndCapturePersisted($this->buildPayload()),
            OrderBilling::class,
        );
    }

    public function testAttachOrderDataPersistsShippingWhenSendShippingTrue(): void
    {
        $this->withCountry();
        $this->withShippingCountry();

        $this->assertPersistedContains(
            $this->attachAndCapturePersisted($this->buildPayload(sendShipping: true, withShipping: true)),
            OrderShipping::class,
        );
    }

    public function testAttachOrderDataSkipsShippingWhenSendShippingFalse(): void
    {
        $this->withCountry();

        $this->entityPersistence
            ->expects($this->exactly(2))
            ->method('persist');

        $this->service->attachOrderData($this->order, $this->buildPayload(sendShipping: false, withShipping: true));
    }

    public function testAttachOrderDataSkipsShippingWhenShippingIsNull(): void
    {
        $this->withCountry();

        $this->entityPersistence
            ->expects($this->exactly(2))
            ->method('persist');

        $this->service->attachOrderData($this->order, $this->buildPayload(sendShipping: true, withShipping: false));
    }

    public function testAttachOrderDataCallsValidateBillingData(): void
    {
        $this->withCountry();

        $this->orderValidatorQuery
            ->expects($this->once())
            ->method('validateBillingData');

        $this->service->attachOrderData($this->order, $this->buildPayload());
    }

    public function testAttachOrderDataCallsValidateShippingData(): void
    {
        $this->withCountry();
        $this->withShippingCountry();

        $this->orderValidatorQuery
            ->expects($this->once())
            ->method('validateShippingData');

        $this->service->attachOrderData($this->order, $this->buildPayload(sendShipping: true, withShipping: true));
    }

    private function withCountry(): void
    {
        $this->countryRepository
            ->method('findById')
            ->willReturn($this->createMock(Country::class));
    }

    private function withShippingCountry(): void
    {
        $this->orderCountryQuery
            ->method('getCountryFromData')
            ->willReturn($this->createMock(Country::class));
    }

    /**
     * @return object[]
    */
    private function attachAndCapturePersisted(OrderCreatePayload $payload): array
    {
        $persisted = [];

        $this->entityPersistence
            ->method('persist')
            ->willReturnCallback(function (object $entity) use (&$persisted): void {
                $persisted[] = $entity;
            });

        $this->service->attachOrderData($this->order, $payload);

        return $persisted;
    }

    private function initMocks(): void
    {
        $this->entityPersistence = $this->createMock(EntityPersistenceContract::class);
        $this->countryRepository = $this->createMock(CountryRepositoryContract::class);
        $this->orderValidatorQuery = $this->createMock(OrderValidatorQueryContract::class);
        $this->orderCountryQuery = $this->createMock(OrderCountryQueryContract::class);
    }

    private function initService(): void
    {
        $this->service = new OrderDataCommandService(
            $this->entityPersistence,
            $this->countryRepository,
            $this->buildQueryBuilder(),
        );
    }

    private function buildQueryBuilder(): OrderQueryBuilder
    {
        return new OrderQueryBuilder(
            orderCountryQuery:     $this->orderCountryQuery,
            orderPreparationQuery: $this->createMock(OrderPreparationQueryContract::class),
            orderItemQuery:        $this->createMock(OrderItemQueryContract::class),
            orderPriceQuery:       $this->createMock(OrderPriceQueryContract::class),
            orderPaymentQuery:     $this->createMock(OrderPaymentQueryContract::class),
            orderValidatorQuery:   $this->orderValidatorQuery,
            orderCacheQuery:       $this->createMock(OrderCacheQueryContract::class),
        );
    }

    private function buildPayload(bool $sendShipping = false, bool $withShipping = false): OrderCreatePayload
    {
        return new OrderCreatePayload(
            personal:      new OrderPersonalObject('test@example.com', 'John', 'Doe'),
            sendShipping:  $sendShipping,
            paymentMethod: OrderPaymentMethod::CASH,
            billing:       new OrderBillingObject(1, 'Main St 1', '12345', 'Bratislava'),
            shipping:      $withShipping
                ? new OrderShippingObject(1, 'Other St 2', '54321', 'Košice')
                : null,
        );
    }
}
