<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Country\Service\Command;

use App\Core\Domain\{
    Segment\Country\Entity\Country,
    Segment\Country\ValueObject\CountryObject
};

use App\Core\Ports\{
    Gateways\External\Api\CountryApiGatewayContract,
    Segment\Country\Service\Command\CountryCommandContract,
    Shared\Logging\AppLoggerContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class CountryCommandService implements CountryCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param CountryApiGatewayContract $countryApiAdapter
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private CountryApiGatewayContract $countryApiAdapter,
        private AppLoggerContract $logger,
    ) {}

    /**
     * @param CountryObject[] $countries
     *
     * @return void
    */
    public function saveCountries(array $countries): void
    {
        foreach ($countries as $apiCountry) {
            if ($this->countryApiAdapter->countryExists($apiCountry->code)) {
                continue;
            }

            $country = $this->createCountry($apiCountry);

            $this->entityPersistence->persist($country);
        }

        $this->flushAndLog();
    }

    /**
     * @param CountryObject $country
     *
     * @return Country
    */
    private function createCountry(CountryObject $country): Country
    {
        return (new Country())
            ->setCode($country->code)
            ->setName($country->name);
    }

    /**
     * @return void
    */
    private function flushAndLog(): void
    {
        try {
            $this->entityPersistence->flush();
        } catch (\Exception $exception) {
            $this->logger->error(
                'Failed to save countries: ' . $exception->getMessage(),
            );

            throw $exception;
        }
    }
}
