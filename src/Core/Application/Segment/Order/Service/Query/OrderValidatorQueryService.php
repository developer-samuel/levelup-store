<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use Kit\{
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
    Segment\Country\Repository\CountryRepositoryContract,
    Segment\Order\Service\Query\OrderValidatorQueryContract
};

use App\Shared\Enum\AddressType;

/**
 * @phpstan-import-type CartItemsResult from OrderValidatorQueryContract
*/
final readonly class OrderValidatorQueryService implements OrderValidatorQueryContract
{
    /**
     * @param CartRepositoryContract $cartRepository
     * @param CountryRepositoryContract $countryRepository
     * @param CartItemQueryContract $cartItemQuery
    */
    public function __construct(
        private CartRepositoryContract $cartRepository,
        private CountryRepositoryContract $countryRepository,
        private CartItemQueryContract $cartItemQuery,
    ) {}

    /**
     * @param User $user
     *
     * @return CartItem[]
    */
    public function getCartItemsOrFail(User $user): array
    {
        $data = $this->validateUserAndGetCartItems($user);

        $cartItems = $data['items'];

        CartItemAssertion::assertNotEmpty($cartItems);

        return $cartItems;
    }

    /**
     * @param User $user
     *
     * @return CartItemsResult
    */
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
        if (empty($items)) {
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

    /**
     * @param OrderBillingObject $billing
     *
     * @return void
     *
     * @throws \InvalidArgumentException
    */
    public function validateBillingData(OrderBillingObject $billing): void
    {
        $this->validateAddressFields($billing, AddressType::BILLING);
    }

    /**
     * @param OrderShippingObject|null $shipping
     *
     * @return void
     *
     * @throws \InvalidArgumentException
    */
    public function validateShippingData(?OrderShippingObject $shipping): void
    {
        if (!$shipping) {
            return;
        }

        $this->validateAddressFields($shipping, AddressType::SHIPPING);
    }

    /**
     * @param OrderBillingObject|OrderShippingObject $address
     * @param AddressType $type
     *
     * @return void
     *
     * @throws \InvalidArgumentException
    */
    private function validateAddressFields(
        OrderBillingObject|OrderShippingObject $address,
        AddressType $type,
    ): void {
        $missing = $this->collectMissingFields($address);

        if (!empty($missing)) {
            throw new \InvalidArgumentException(sprintf(
                'Missing %s fields: %s',
                strtolower($type->name),
                implode(', ', $missing),
            ));
        }
    }

    /**
     * @param OrderBillingObject|OrderShippingObject $address
     *
     * @return string[]
    */
    private function collectMissingFields(OrderBillingObject|OrderShippingObject $address): array
    {
        $missing = [];

        if ($this->isCountryMissing($address)) {
            $missing[] = 'country';
        }

        foreach (['street', 'postalCode', 'city'] as $key) {
            if (!isset($address->{$key}) || trim((string) $address->{$key}) === '') {
                $missing[] = $key === 'postalCode' ? 'postal code' : $key;
            }
        }

        return $missing;
    }

    /**
     * @param OrderBillingObject|OrderShippingObject $address
     *
     * @return bool
    */
    private function isCountryMissing(OrderBillingObject|OrderShippingObject $address): bool
    {
        return !isset($address->country) || $this->countryRepository->findById((int) $address->country) === null;
    }
}
