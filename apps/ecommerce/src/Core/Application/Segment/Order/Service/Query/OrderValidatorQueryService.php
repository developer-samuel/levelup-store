<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use Packages\Kit\{
    Assertion\Domain\Cart\CartItemAssertion,
    Assertion\Shared\IdAssertion,
};

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject,
    Segment\User\Entity\User,
};

use App\Core\Ports\{
    Segment\Cart\Repository\CartRepositoryContract,
    Segment\Cart\Service\Query\CartItemQueryContract,
    Segment\Country\CountryRepositoryContract,
    Segment\Order\Service\Query\OrderValidatorQueryContract
};

use App\Shared\Enum\AddressType;

/** @phpstan-import-type CartItemsResult from OrderValidatorQueryContract */
final readonly class OrderValidatorQueryService implements OrderValidatorQueryContract
{
    public function __construct(
        private CartRepositoryContract $cartRepository,
        private CountryRepositoryContract $countryRepository,
        private CartItemQueryContract $cartItemQuery,
    ) {}

    /** @return CartItem[] */
    public function getCartItemsOrFail(User $user): array
    {
        $data = $this->validateUserAndGetCartItems($user);

        $cartItems = $data['items'];

        CartItemAssertion::assertNotEmpty($cartItems);

        return $cartItems;
    }

    /** @return CartItemsResult */
    public function validateUserAndGetCartItems(User $user): array
    {
        $userId = IdAssertion::assert(
            $user->getId(),
            'User ID',
        );

        $cart = $this->cartRepository->findCartForUser($userId);
        if ($cart === null) {
            return [
                'cart'  => null,
                'items' => [],
            ];
        }

        $items = $this->cartItemQuery->getItems($user);
        if ($items === []) {
            return [
                'cart'  => null,
                'items' => [],
            ];
        }

        return [
            'cart'  => $cart,
            'items' => $items,
        ];
    }

    public function validateBillingData(OrderBillingObject $billing): void
    {
        $this->validateAddressFields($billing, AddressType::BILLING);
    }

    public function validateShippingData(?OrderShippingObject $shipping): void
    {
        if ($shipping === null) {
            return;
        }

        $this->validateAddressFields($shipping, AddressType::SHIPPING);
    }

    private function validateAddressFields(
        OrderBillingObject|OrderShippingObject $address,
        AddressType $type,
    ): void {
        $missing = $this->collectMissingFields($address);

        if ($missing !== []) {
            throw new \InvalidArgumentException(sprintf(
                'Missing %s fields: %s',
                strtolower($type->name),
                implode(', ', $missing),
            ));
        }
    }

    /** @return string[] */
    private function collectMissingFields(OrderBillingObject|OrderShippingObject $address): array
    {
        $missing = [];

        if ($this->isCountryMissing($address)) {
            $missing[] = 'country';
        }

        foreach (['street', 'postalCode', 'city'] as $key) {
            if (!isset($address->{$key}) || trim($address->{$key}) === '') {
                $missing[] = $key === 'postalCode' ? 'postal code' : $key;
            }
        }

        return $missing;
    }

    private function isCountryMissing(OrderBillingObject|OrderShippingObject $address): bool
    {
        return !isset($address->country) || $this->countryRepository->findById($address->country) === null;
    }
}
