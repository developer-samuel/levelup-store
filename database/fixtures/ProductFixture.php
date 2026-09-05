<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Doctrine\{
    Common\DataFixtures\DependentFixtureInterface,
    Persistence\ObjectManager
};

use Database\{
    Seeds\Abstract\AbstractFixture,
    Seeds\Builders\ProductBuilder,
    Seeds\Records\Contracts\ProductRecordContract,
    Seeds\Records\Product\AppliancesRecord,
    Seeds\Records\Product\ComputersRecord,
    Seeds\Records\Product\GamingRecord,
    Seeds\Records\Product\SmartRecord,
    Seeds\Records\Product\TvRecord,
};

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Type\Entity\Type
};

use App\Core\Ports\{
    Segment\Brand\BrandRepositoryContract,
    Segment\Category\Repository\CategoryRepositoryContract,
    Segment\Type\TypeRepositoryContract,
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

class ProductFixture extends AbstractFixture implements DependentFixtureInterface
{
    use ProductBuilder;

    /**
     * @param BrandRepositoryContract $brandRepository
     * @param CategoryRepositoryContract $categoryRepository
     * @param TypeRepositoryContract $typeRepository
     * @param AppLoggerContract $appLogger
     * @param ConsoleLoggerContract $consoleLogger
    */
    public function __construct(
        private readonly BrandRepositoryContract $brandRepository,
        private readonly CategoryRepositoryContract $categoryRepository,
        private readonly TypeRepositoryContract $typeRepository,
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
            BrandFixture::class,
            SubtypeFixture::class,
        ];
    }

    /**
     * @return iterable<ProductRecordContract>
    */
    protected function getData(): iterable
    {
        /** @var iterable<ProductRecordContract> $data */
        $data = [
            new ComputersRecord(),
            new SmartRecord(),
            new GamingRecord(),
            new TvRecord(),
            new AppliancesRecord(),
        ];

        return $data;
    }

    /**
     * @param ProductRecordContract $record
     * @param ObjectManager $manager
     *
     * @return void
    */
    protected function createEntity(mixed $record, ObjectManager $manager): void
    {
        /** @var array<string, array<string, mixed>> $productDataSet */
        $productDataSet = $record->fetchData();

        foreach ($productDataSet as $categoryName => $types) {
            $this->processCategory($manager, (string) $categoryName, $types);
        }
    }

    /**
     * @param ObjectManager $manager
     * @param string $categoryName
     * @param array<string, mixed> $types
     *
     * @return void
    */
    private function processCategory(ObjectManager $manager, string $categoryName, array $types): void
    {
        $category = $this->categoryRepository->findByName($categoryName);
        if ($category === null) {
            $this->consoleLogger->logError(sprintf("Category '%s' not found.", $categoryName));
            return;
        }

        foreach ($types as $typeName => $products) {
            $this->processType($manager, $category, (string) $typeName, $products);
        }
    }

    /**
     * @param ObjectManager $manager
     * @param Category $category
     * @param string $typeName
     * @param mixed $products
     *
     * @return void
    */
    private function processType(ObjectManager $manager, Category $category, string $typeName, mixed $products): void
    {
        $type = $this->typeRepository->findByName($typeName);

        if ($type === null) {
            $this->consoleLogger->logError(sprintf("Type '%s' not found.", $typeName));
            return;
        }

        if (!is_array($products)) {
            $this->consoleLogger->logError(sprintf("Products for type '%s' are not an array.", $typeName));
            return;
        }

        foreach ($products as $productName => $productData) {
            /**
             * @var array{
             *     brand: string,
             *     variants: array<string, array{
             *     price: float,
             *     description: string,
             *     discountPrice: float|null,
             *     stocks_available: int,
             *     stocks_reserved: int,
             *     images: string[],
             *     descriptions: array<array{string, string}>
             * }>,
             *     subtypes: string[]
             * } $productData
            */
            $this->processSingleProduct($manager, $productName, $productData, $category, $type);
        }
    }

    /**
     * @param ObjectManager $manager
     * @param string $productName
     * @param array{
     *     brand: string,
     *     variants: array<string, array{
     *     price: float,
     *     description: string,
     *     discountPrice: float|null,
     *     stocks_available: int,
     *     stocks_reserved: int,
     *     images: string[],
     *     descriptions: array<array{string, string}>
     * }>,
     *     subtypes: string[]
     * } $productData
     * @param Category $category
     * @param Type $type
     *
     * @return void
    */
    private function processSingleProduct(
        ObjectManager $manager,
        string $productName,
        array $productData,
        Category $category,
        Type $type,
    ): void {
        try {
            $this->createProductWithVariants($manager, $productName, $productData, $category, $type);
        } catch (\Throwable $throwable) {
            $this->consoleLogger->logError(
                sprintf("Failed to create product '%s': %s", $productName, $throwable->getMessage()),
            );
        }
    }
}
