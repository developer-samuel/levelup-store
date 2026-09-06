<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Service\Query;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Country\Entity\Country,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject,
    Segment\User\Entity\User
};

use App\Core\Application\Segment\Order\Service\Query\OrderValidatorQueryService;

use App\Core\Ports\{
    Segment\Cart\Repository\CartRepositoryContract,
    Segment\Cart\Service\Query\CartItemQueryContract,
    Segment\Country\CountryRepositoryContract,
    Segment\Order\Service\Query\OrderValidatorQueryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Query\OrderValidatorQueryService
*/
final class OrderValidatorQueryServiceTest extends TestCase
{
    private CartRepositoryContract&MockObject $cartRepository;
    private CountryRepositoryContract&MockObject $countryRepository;
    private CartItemQueryContract&MockObject $cartItemQuery;
    private OrderValidatorQueryService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderValidatorQueryContract::class, $this->service);
    }

    public function testValidateUserAndGetCartItemsReturnsEmptyWhenNoCart(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn(null);

        $result = $this->service->validateUserAndGetCartItems($this->buildUserMock());

        $this->assertNull($result['cart']);
        $this->assertEmpty($result['items']);
    }

    public function testValidateUserAndGetCartItemsReturnsEmptyWhenCartExistsButNoItems(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn($this->createMock(Cart::class));
        $this->cartItemQuery->method('getItems')->willReturn([]);

        $result = $this->service->validateUserAndGetCartItems($this->buildUserMock());

        $this->assertNull($result['cart']);
        $this->assertEmpty($result['items']);
    }

    public function testValidateUserAndGetCartItemsReturnsCartAndItems(): void
    {
        [$cart, $items] = $this->withCartAndItems();

        $result = $this->service->validateUserAndGetCartItems($this->buildUserMock());

        $this->assertSame($cart, $result['cart']);
        $this->assertSame($items, $result['items']);
    }

    public function testGetCartItemsOrFailReturnsItemsWhenCartHasItems(): void
    {
        [, $items] = $this->withCartAndItems();

        $result = $this->service->getCartItemsOrFail($this->buildUserMock());

        $this->assertSame($items, $result);
    }

    public function testGetCartItemsOrFailThrowsWhenCartIsEmpty(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn(null);

        $this->expectException(\RuntimeException::class);

        $this->service->getCartItemsOrFail($this->buildUserMock());
    }

    public function testValidateBillingDataPassesWithValidData(): void
    {
        $this->expectNotToPerformAssertions();
        $this->withValidCountry();

        $this->service->validateBillingData($this->buildValidBilling());
    }

    public function testValidateBillingDataThrowsWhenCountryNotFound(): void
    {
        $this->withMissingCountry();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/country/i');

        $this->service->validateBillingData($this->buildValidBilling());
    }

    public function testValidateBillingDataThrowsWhenStreetIsEmpty(): void
    {
        $this->withValidCountry();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/street/i');

        $this->service->validateBillingData(new OrderBillingObject(
            country:    1,
            street:     '',
            postalCode: '12345',
            city:       'Bratislava',
        ));
    }

    public function testValidateBillingDataThrowsWhenPostalCodeIsEmpty(): void
    {
        $this->withValidCountry();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/postal code/i');

        $this->service->validateBillingData(new OrderBillingObject(
            country:    1,
            street:     'Main St 1',
            postalCode: '',
            city:       'Bratislava',
        ));
    }

    public function testValidateBillingDataThrowsWhenCityIsEmpty(): void
    {
        $this->withValidCountry();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/city/i');

        $this->service->validateBillingData(new OrderBillingObject(
            country:    1,
            street:     'Main St 1',
            postalCode: '12345',
            city:       '',
        ));
    }

    public function testValidateShippingDataPassesWhenNull(): void
    {
        $this->expectNotToPerformAssertions();

        $this->service->validateShippingData(null);
    }

    public function testValidateShippingDataPassesWithValidData(): void
    {
        $this->expectNotToPerformAssertions();
        $this->withValidCountry();

        $this->service->validateShippingData($this->buildValidShipping());
    }

    public function testValidateShippingDataThrowsWhenStreetIsEmpty(): void
    {
        $this->withValidCountry();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/street/i');

        $this->service->validateShippingData(new OrderShippingObject(
            country:    1,
            street:     '',
            postalCode: '12345',
            city:       'Bratislava',
        ));
    }

    public function testValidateShippingDataThrowsWhenCountryNotFound(): void
    {
        $this->withMissingCountry();

        $this->expectException(\InvalidArgumentException::class);

        $this->service->validateShippingData($this->buildValidShipping());
    }

    private function initMocks(): void
    {
        $this->cartRepository = $this->createMock(CartRepositoryContract::class);
        $this->countryRepository = $this->createMock(CountryRepositoryContract::class);
        $this->cartItemQuery = $this->createMock(CartItemQueryContract::class);
    }

    private function initService(): void
    {
        $this->service = new OrderValidatorQueryService(
            $this->cartRepository,
            $this->countryRepository,
            $this->cartItemQuery,
        );
    }

    private function buildUserMock(): User&MockObject
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);

        return $user;
    }

    /**
     * @return array{0: Cart&MockObject, 1: list<CartItem&MockObject>}
    */
    private function withCartAndItems(): array
    {
        $cart = $this->createMock(Cart::class);
        $items = [$this->createMock(CartItem::class)];

        $this->cartRepository->method('findCartForUser')->willReturn($cart);
        $this->cartItemQuery->method('getItems')->willReturn($items);

        return [$cart, $items];
    }

    private function withValidCountry(): void
    {
        $this->countryRepository
            ->method('findById')
            ->willReturn($this->createMock(Country::class));
    }

    private function withMissingCountry(): void
    {
        $this->countryRepository->method('findById')->willReturn(null);
    }

    private function buildValidBilling(): OrderBillingObject
    {
        return new OrderBillingObject(
            country:    1,
            street:     'Main St 1',
            postalCode: '12345',
            city:       'Bratislava',
        );
    }

    private function buildValidShipping(): OrderShippingObject
    {
        return new OrderShippingObject(
            country:    1,
            street:     'Main St 1',
            postalCode: '12345',
            city:       'Bratislava',
        );
    }
}
