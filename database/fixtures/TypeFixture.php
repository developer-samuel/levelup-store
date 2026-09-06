<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Doctrine\{
    Common\DataFixtures\DependentFixtureInterface,
    Persistence\ObjectManager
};

use Database\{
    Seeds\Abstract\AbstractFixture,
    Seeds\Behavior\Segment\HasCategory,
    Seeds\Factories\TypeFactory,
    Seeds\Records\TypeRecord
};

use App\Core\Ports\{
    Segment\Category\Repository\CategoryRepositoryContract,
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

final class TypeFixture extends AbstractFixture implements DependentFixtureInterface
{
    use HasCategory;
    use TypeFactory;

    /**
     * @param CategoryRepositoryContract $categoryRepository
     * @param TypeRecord $typeRecord
     * @param AppLoggerContract $appLogger
     * @param ConsoleLoggerContract $consoleLogger
    */
    public function __construct(
        private readonly CategoryRepositoryContract $categoryRepository,
        private readonly TypeRecord $typeRecord,
        AppLoggerContract $appLogger,
        ConsoleLoggerContract $consoleLogger,
    ) {
        parent::__construct(
            $appLogger,
            $consoleLogger,
        );
    }

    /**
     * @return string[]
    */
    public function getDependencies(): array
    {
        return [
            CategoryFixture::class,
        ];
    }

    /**
     * @return iterable<array{
     *     categoryName: string,
     *     types: string[]
     * }>
    */
    protected function getData(): iterable
    {
        $data = $this->typeRecord->fetchData();

        foreach ($data as $categoryName => $types) {
            yield [
                'categoryName' => $categoryName,
                'types'        => $types,
            ];
        }
    }

    /**
     * @param array{
     *     categoryName: string,
     *     types: string[]
     * } $data
     * @param ObjectManager $manager
     *
     * @return void
    */
    protected function createEntity(mixed $data, ObjectManager $manager): void
    {
        $category = $this->findCategoryOrLog($data['categoryName']);
        if ($category === null) {
            return;
        }

        $this->createAndPersistTypes($manager, $category, $data['types']);
    }
}
