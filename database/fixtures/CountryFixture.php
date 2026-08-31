<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Doctrine\{
    Bundle\FixturesBundle\FixtureGroupInterface,
    Persistence\ObjectManager
};

use Database\Seeds\Abstract\AbstractFixture;

use App\Core\Domain\Segment\Country\ValueObject\CountryObject;

use App\Core\Ports\{
    Gateways\External\Api\CountryApiGatewayContract,
    Segment\Country\Service\Command\CountryCommandContract,
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

class CountryFixture extends AbstractFixture implements FixtureGroupInterface
{
    /**
     * @param CountryApiGatewayContract $countryApiAdapter
     * @param CountryCommandContract $countryCommand
     * @param AppLoggerContract $appLogger
     * @param ConsoleLoggerContract $consoleLogger
    */
    public function __construct(
        private readonly CountryApiGatewayContract $countryApiAdapter,
        private readonly CountryCommandContract $countryCommand,
        AppLoggerContract $appLogger,
        ConsoleLoggerContract $consoleLogger,
    ) {
        parent::__construct(
            $appLogger,
            $consoleLogger,
        );
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
    */
    public function load(ObjectManager $manager): void
    {
        $countries = $this->resolveCountries();

        if ($countries !== []) {
            $this->countryCommand->saveCountries($countries);
        }
    }

    /**
     * @return iterable<CountryObject>
    */
    protected function getData(): iterable
    {
        $countries = $this->countryApiAdapter->getAllCountries();

        if ($countries === null) {
            $this->consoleLogger->logError('Failed to fetch countries data');

            return [];
        }

        return $countries;
    }

    /**
     * @param mixed $data
     * @param ObjectManager $manager
     *
     * @return void
    */
    protected function createEntity(mixed $data, ObjectManager $manager): void
    {
        //
    }

    /**
     * @return CountryObject[]
    */
    private function resolveCountries(): array
    {
        $countries = $this->getData();

        return is_array($countries) ? $countries : iterator_to_array($countries);
    }
}
