<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Doctrine\{
    Bundle\FixturesBundle\FixtureGroupInterface,
    Persistence\ObjectManager
};

use Database\{
    Seeds\Abstract\AbstractFixture,
    Seeds\Factories\CategoryFactory,
    Seeds\Records\CategoryRecord,
    Seeds\Utils\Sanitizer\NameSanitizer
};

use App\Core\Ports\{
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

final class CategoryFixture extends AbstractFixture implements FixtureGroupInterface
{
    use CategoryFactory;
    use NameSanitizer;

    public function __construct(
        private readonly CategoryRecord $categoryRecord,
        AppLoggerContract $appLogger,
        ConsoleLoggerContract $consoleLogger,
    ) {
        parent::__construct(
            $appLogger,
            $consoleLogger,
        );
    }

    /** @return iterable<array<string>> */
    protected function getData(): iterable
    {
        return $this->categoryRecord->fetchData();
    }

    protected function createEntity(mixed $data, ObjectManager $manager): void
    {
        $name = $this->sanitize($data);

        $category = $this->createCategory($name);

        $manager->persist($category);
    }
}
