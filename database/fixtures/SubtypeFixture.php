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
    Seeds\Factories\SubtypeFactory,
    Seeds\Records\SubtypeRecord
};

use Kit\Utils\Shared\DataSanitizer;

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Type\Entity\Type
};

use App\Core\Ports\{
    Segment\Category\Repository\CategoryRepositoryContract,
    Segment\Type\TypeRepositoryContract,
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

final class SubtypeFixture extends AbstractFixture implements DependentFixtureInterface
{
    use HasCategory;
    use SubtypeFactory;

    /**
     * @param CategoryRepositoryContract $categoryRepository
     * @param TypeRepositoryContract $typeRepository
     * @param SubtypeRecord $subtypeRecord
     * @param AppLoggerContract $appLogger
     * @param ConsoleLoggerContract $consoleLogger
    */
    public function __construct(
        private readonly CategoryRepositoryContract $categoryRepository,
        private readonly TypeRepositoryContract $typeRepository,
        private readonly SubtypeRecord $subtypeRecord,
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
            TypeFixture::class,
        ];
    }

    /**
     * @return iterable<array{
     *     categoryName: string,
     *     typeName: string,
     *     subtypes: string[]
     * }>
    */
    protected function getData(): iterable
    {
        $data = $this->subtypeRecord->fetchData();

        foreach ($data as $categoryName => $types) {
            yield from $this->yieldSubtypes((string) $categoryName, $types);
        }
    }

    /**
     * @param array{
     *     categoryName: string,
     *     typeName: string,
     *     subtypes: string[]
     * } $data
     * @param ObjectManager $manager
     *
     * @return void
    */
    protected function createEntity(mixed $data, ObjectManager $manager): void
    {
        $category = $this->findCategoryOrLog($data['categoryName']);
        if ($category === null) return;

        $type = $this->findTypeOrLog($category, $data['typeName']);
        if ($type === null) return;

        $this->createSubtypes($manager, $category, $type, $data['subtypes']);
    }

    /**
     * @param string $categoryName
     * @param string[] $types
     *
     * @return iterable<array{
     *     categoryName: string,
     *     typeName: string,
     *     subtypes: string[]
     * }>
    */
    private function yieldSubtypes(string $categoryName, array $types): iterable
    {
        foreach ($types as $typeName => $subtypes) {
            $subtypesList = is_array($subtypes) ? $subtypes : [$subtypes];

            yield [
                'categoryName' => $categoryName,
                'typeName'     => (string) $typeName,
                'subtypes'     => DataSanitizer::sanitizeStringArray($subtypesList),
            ];
        }
    }

    /**
     * @param Category $category
     * @param string $typeName
     *
     * @return Type|null
    */
    private function findTypeOrLog(Category $category, string $typeName): ?Type
    {
        $type = $this->getType($this->typeRepository, $category, $typeName);
        if ($type === null) {
            $this->consoleLogger->logError(sprintf("Type '%s' not found in '%s'.", $typeName, $category->getName()));
        }

        return $type;
    }
}
