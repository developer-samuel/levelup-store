<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Command;

use App\Core\Application\Segment\Order\Builder\OrderQueryBuilder;

use App\Core\Domain\{
    Segment\Country\Entity\Country,
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderPersonal,
    Segment\Order\Entity\OrderBilling,
    Segment\Order\Entity\OrderShipping,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject,
    Segment\Order\ValueObject\OrderPersonalObject
};

use App\Core\Ports\{
    Segment\Country\CountryRepositoryContract,
    Segment\Order\Service\Command\OrderDataCommandContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class OrderDataCommandService implements OrderDataCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param CountryRepositoryContract $countryRepository
     * @param OrderQueryBuilder $orderQueryBuilder
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private CountryRepositoryContract $countryRepository,
        private OrderQueryBuilder $orderQueryBuilder,
    ) {}

    /**
     * @param Order $order
     * @param OrderCreatePayload $payload
     *
     * @return void
    */
    public function attachOrderData(Order $order, OrderCreatePayload $payload): void
    {
        $this->createPersonalData($order, $payload->personal);
        $this->createBillingData($order, $payload->billing);

        if ($payload->sendShipping && $payload->shipping !== null) {
            $this->createShippingData($order, $payload->shipping);
        }
    }

    /**
     * @param Order $order
     * @param OrderPersonalObject $personal
     *
     * @return void
    */
    private function createPersonalData(Order $order, OrderPersonalObject $personal): void
    {
        $personal = (new OrderPersonal())
            ->setOrder($order)
            ->setEmail($personal->email)
            ->setFirstName($personal->firstName)
            ->setLastName($personal->lastName);

        $this->entityPersistence->persist($personal);
    }

    /**
     * @param Order $order
     * @param OrderBillingObject $billing
     *
     * @return void
    */
    private function createBillingData(Order $order, OrderBillingObject $billing): void
    {
        $this->orderQueryBuilder->orderValidatorQuery->validateBillingData($billing);

        $country = $this->countryRepository->findById($billing->country);

        $billing = (new OrderBilling())
            ->setOrder($order)
            ->setCountry($country)
            ->setStreet($billing->street)
            ->setPostalCode($billing->postalCode)
            ->setCity($billing->city);

        $this->entityPersistence->persist($billing);
    }

    /**
     * @param Order $order
     * @param OrderShippingObject $shipping
     *
     * @return void
    */
    private function createShippingData(Order $order, OrderShippingObject $shipping): void
    {
        $this->orderQueryBuilder->orderValidatorQuery->validateShippingData($shipping);

        $country = $this->orderQueryBuilder->orderCountryQuery->getCountryFromData($shipping);
        $shipping = $this->createOrderShippingEntity($order, $country, $shipping);

        $this->entityPersistence->persist($shipping);
    }

    /**
     * @param Order $order
     * @param Country $country
     * @param OrderShippingObject $shipping
     *
     * @return OrderShipping
    */
    private function createOrderShippingEntity(Order $order, Country $country, OrderShippingObject $shipping): OrderShipping
    {
        return (new OrderShipping())
            ->setOrder($order)
            ->setCountry($country)
            ->setStreet($shipping->street)
            ->setPostalCode($shipping->postalCode)
            ->setCity($shipping->city);
    }
}
