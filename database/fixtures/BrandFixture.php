<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Doctrine\{
    Bundle\FixturesBundle\FixtureGroupInterface,
    Persistence\ObjectManager
};

use Database\{
    Seeds\Abstract\AbstractFixture,
    Seeds\Factories\BrandFactory,
    Seeds\Records\BrandRecord,
    Seeds\Utils\Sanitizer\NameSanitizer
};

use App\Core\Ports\{
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

final class BrandFixture extends AbstractFixture implements FixtureGroupInterface
{
    use BrandFactory;
    use NameSanitizer;

    /**
     * @param BrandRecord $brandRecord
     * @param AppLoggerContract $appLogger
     * @param ConsoleLoggerContract $consoleLogger
    */
    public function __construct(
        private readonly BrandRecord $brandRecord,
        AppLoggerContract $appLogger,
        ConsoleLoggerContract $consoleLogger,
    ) {
        parent::__construct(
            $appLogger,
            $consoleLogger,
        );
    }

    /**
     * @return iterable<mixed>
    */
    protected function getData(): iterable
    {
        return $this->brandRecord->fetchData();
    }

    /**
     * @param mixed $data
     * @param ObjectManager $manager
     *
     * @return void
    */
    protected function createEntity(mixed $data, ObjectManager $manager): void
    {
        $name = $this->sanitize($data);

        $brand = $this->createBrand($name);

        $manager->persist($brand);
    }
}
