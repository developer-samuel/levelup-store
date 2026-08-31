<?php

declare(strict_types=1);

namespace App\Scheduler\Task\Country;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\{
    Segment\Country\Entity\Country,
    Segment\Country\ValueObject\CountryObject
};

use App\Core\Ports\{
    Gateways\External\Api\CountryApiGatewayContract,
    Shared\Logging\ConsoleLoggerContract
};

use App\Scheduler\{
    Message\Country\CountrySyncMessage,
    Task\Abstract\AbstractTask
};

#[AsMessageHandler]
class CountrySyncTask extends AbstractTask
{
    /**
     * @param CountryApiGatewayContract $countryApiAdapter
     * @param EntityManagerInterface $entityManager
     * @param ConsoleLoggerContract $logger
    */
    public function __construct(
        private readonly CountryApiGatewayContract $countryApiAdapter,
        EntityManagerInterface $entityManager,
        ConsoleLoggerContract $logger,
    ) {
        parent::__construct($entityManager, $logger);
    }

    /**
     * @param CountrySyncMessage $message
     *
     * @return void
    */
    public function __invoke(CountrySyncMessage $message): void
    {
        $this->execute();
    }

    /**
     * @return string
    */
    protected function getTaskName(): string
    {
        return 'CountrySyncTask';
    }

    /**
     * @return CountryObject[]
    */
    protected function fetchItems(): iterable
    {
        return $this->countryApiAdapter->getAllCountries() ?? [];
    }

    /**
     * @param iterable<CountryObject> $items
     *
     * @return int
    */
    protected function processItems(iterable $items): int
    {
        $addedCount = 0;

        foreach ($items as $countryObject) {
            if ($this->processSingleCountry($countryObject)) {
                $addedCount++;
            }
        }

        if ($addedCount > 0) {
            $this->entityManager->flush();
        }

        return $addedCount;
    }

    /**
     * @param CountryObject $countryObject
     *
     * @return bool
    */
    private function processSingleCountry(CountryObject $countryObject): bool
    {
        if ($this->countryApiAdapter->countryExists($countryObject->code)) {
            return false;
        }

        $this->entityManager->persist($this->createCountry($countryObject));

        return true;
    }

    /**
     * @param CountryObject $countryObject
     *
     * @return Country
    */
    private function createCountry(CountryObject $countryObject): Country
    {
        return (new Country())
            ->setCode($countryObject->code)
            ->setName($countryObject->name);
    }
}
