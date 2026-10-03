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

/** @extends AbstractTask<CountryObject> */
#[AsMessageHandler]
final class CountrySyncTask extends AbstractTask
{
    public function __construct(
        private readonly CountryApiGatewayContract $countryApiAdapter,
        EntityManagerInterface $entityManager,
        ConsoleLoggerContract $logger,
    ) {
        parent::__construct($entityManager, $logger);
    }

    public function __invoke(CountrySyncMessage $message): void
    {
        $this->execute();
    }

    protected function getTaskName(): string
    {
        return 'CountrySyncTask';
    }

    /** @return CountryObject[] */
    protected function fetchItems(): iterable
    {
        return $this->countryApiAdapter->getAllCountries() ?? [];
    }

    /** @param iterable<CountryObject> $items */
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

    private function processSingleCountry(CountryObject $countryObject): bool
    {
        if ($this->countryApiAdapter->countryExists($countryObject->code)) {
            return false;
        }

        $this->entityManager->persist($this->createCountry($countryObject));

        return true;
    }

    private function createCountry(CountryObject $countryObject): Country
    {
        return (new Country())
            ->setCode($countryObject->code)
            ->setName($countryObject->name);
    }
}
