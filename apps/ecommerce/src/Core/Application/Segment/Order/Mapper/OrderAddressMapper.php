<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Mapper;

use Kit\Assertion\Domain\Order\OrderBillingAssertion;

use App\Core\Domain\{
    Segment\Country\Utils\CountryTransformer,
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling,
    Segment\Order\Entity\OrderShipping
};

use App\Core\Application\{
    Shared\Enum\CaseType,
    Shared\Utils\Mapper\ArrayMapper
};

/**
 * @phpstan-type AddressSnake array{
 *     country: string,
 *     city: string,
 *     postal_code: string,
 *     street: string
 * }
 * @phpstan-type AddressCamel array{
 *     country: string,
 *     city: string,
 *     postalCode: string,
 *     street: string
 * }
 * @phpstan-type FullAddress array{
 *     id: int|string|null,
 *     order_id: int|string|null,
 *     country: array<string, int|string|null>|null,
 *     street: string|null,
 *     postal_code: string|null,
 *     city: string|null
 * }
*/
final class OrderAddressMapper
{
    /**
     * @param OrderBilling|OrderShipping $address
     *
     * @return FullAddress
    */
    public static function mapFullAddress(OrderBilling|OrderShipping $address): array
    {
        return [
            'id'          => $address->getId(),
            'order_id'    => $address->getId(),
            'country'     => CountryTransformer::transformCountry($address->getCountry()),
            'street'      => $address->getStreet(),
            'postal_code' => $address->getPostalCode(),
            'city'        => $address->getCity(),
        ];
    }

    /**
     * @param Order $order
     *
     * @return AddressCamel
    */
    public static function mapBillingCamelCase(Order $order): array
    {
        $billing = OrderBillingAssertion::assertBillingExists($order);

        /** @var AddressCamel $data */
        $data = self::mapAddress($billing, OrderBilling::class, CaseType::CAMEL);

        return $data;
    }

    /**
     * @param Order $order
     *
     * @return AddressCamel
    */
    public static function mapShippingCamelCase(Order $order): array
    {
        /** @var AddressCamel $data */
        $data = self::mapAddress($order->getShipping(), OrderShipping::class, CaseType::CAMEL);

        return $data;
    }

    /**
     * @param Order $order
     *
     * @return AddressSnake
    */
    public static function mapBillingSnakeCase(Order $order): array
    {
        /** @var AddressSnake $data */
        $data = self::mapAddress($order->getBilling(), OrderBilling::class, CaseType::SNAKE);

        return $data;
    }

    /**
     * @param Order $order
     *
     * @return AddressSnake
    */
    public static function mapShippingSnakeCase(Order $order): array
    {
        /** @var AddressSnake $data */
        $data = self::mapAddress($order->getShipping(), OrderShipping::class, CaseType::SNAKE);

        return $data;
    }

    /**
     * @param OrderBilling|OrderShipping|null $address
     * @param string $expectedClass
     * @param CaseType $case
     *
     * @return AddressCamel|AddressSnake
    */
    private static function mapAddress(?object $address, string $expectedClass, CaseType $case): array
    {
        if (!$address instanceof $expectedClass) {
            /** @var AddressCamel|AddressSnake */
            return ArrayMapper::emptyByKeys(self::getKeysByCase($case));
        }

        $values = self::extractAddressValues($address);
        $keys = self::getKeysByCase($case);

        /** @var AddressCamel|AddressSnake */
        return ArrayMapper::mapValuesToKeys($values, $keys);
    }

    /**
     * @param OrderBilling|OrderShipping $address
     *
     * @return array{
     *     country: string,
     *     city: string,
     *     postal: string,
     *     street: string
     * }
    */
    private static function extractAddressValues(object $address): array
    {
        return [
            'country' => $address->getCountry()?->getName() ?? '',
            'city'    => $address->getCity() ?? '',
            'postal'  => $address->getPostalCode() ?? '',
            'street'  => $address->getStreet() ?? '',
        ];
    }

    /**
     * @param CaseType $case
     *
     * @return string[]
    */
    private static function getKeysByCase(CaseType $case): array
    {
        return [
            'country' => 'country',
            'city'    => 'city',
            'postal'  => $case === CaseType::CAMEL ? 'postalCode' : 'postal_code',
            'street'  => 'street',
        ];
    }
}
