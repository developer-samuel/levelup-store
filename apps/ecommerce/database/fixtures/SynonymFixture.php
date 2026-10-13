<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Doctrine\{
    Bundle\FixturesBundle\FixtureGroupInterface,
    Persistence\ObjectManager
};

use Database\{
    Seeds\Abstract\AbstractFixture,
    Seeds\Factories\SynonymFactory,
    Seeds\Records\SynonymRecord
};

use App\Core\Ports\{
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

final class SynonymFixture extends AbstractFixture implements FixtureGroupInterface
{
    use SynonymFactory;

    public function __construct(
        private readonly SynonymRecord $synonymRecord,
        AppLoggerContract $appLogger,
        ConsoleLoggerContract $consoleLogger,
    ) {
        parent::__construct(
            $appLogger,
            $consoleLogger,
        );
    }

    /** @return iterable<mixed> */
    protected function getData(): iterable
    {
        return $this->synonymRecord->fetchData();
    }

    protected function createEntity(mixed $data, ObjectManager $manager): void
    {
        if (!is_string($data)) {
            return;
        }

        $synonym = $this->createSynonym($data);

        $manager->persist($synonym);
    }
}
