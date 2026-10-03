<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use Packages\Kit\{
    Assertion\Domain\Country\CountryAssertion,
    Assertion\Shared\IdAssertion
};

use App\Core\Domain\{
    Segment\Country\Entity\Country,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject
};

use App\Core\Ports\{
    Segment\Country\CountryRepositoryContract,
    Segment\Order\Service\Query\OrderCountryQueryContract
};

final readonly class OrderCountryQueryService implements OrderCountryQueryContract
{
    public function __construct(
        private CountryRepositoryContract $countryRepository,
    ) {}

    public function getCountryFromData(OrderBillingObject|OrderShippingObject $data): Country
    {
        $countryIdRaw = $data->country;

        $this->validateCountryId($countryIdRaw);
        $countryId = $countryIdRaw;

        $country = $this->countryRepository->findById($countryId);
        $this->validateCountryForOrderShipping($countryId, $country);

        assert($country instanceof Country);

        return $country;
    }

    private function validateCountryId(int|string|null $countryIdRaw): void
    {
        IdAssertion::assertType($countryIdRaw, 'Country ID');
        IdAssertion::assertNumeric($countryIdRaw, 'Country ID');
    }

    private function validateCountryForOrderShipping(int $countryId, ?Country $country): void
    {
        $this->checkShippingCountryExists($countryId);
        CountryAssertion::assertExistsForId($country, $countryId);
    }

    private function checkShippingCountryExists(?int $countryId): void
    {
        if ($countryId === null || $this->countryRepository->findById($countryId) === null) {
            throw new \InvalidArgumentException('Shipping country not found.');
        }
    }
}
