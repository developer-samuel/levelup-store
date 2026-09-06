<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Product\Service\Query;

use Doctrine\Common\Collections\ArrayCollection;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Subtype\Entity\Subtype,
    Segment\Type\Entity\Type
};

use App\Core\Application\Segment\Product\Service\Query\ProductCategoryQueryService;

use App\Core\Ports\{
    Segment\Category\Repository\CategoryRepositoryContract,
    Segment\Product\Service\Query\ProductCategoryQueryContract,
    Segment\Type\TypeRepositoryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Product\Service\Query\ProductCategoryQueryService
*/
final class ProductCategoryQueryServiceTest extends TestCase
{
    private CategoryRepositoryContract&MockObject $categoryRepository;
    private TypeRepositoryContract&MockObject $typeRepository;
    private ProductCategoryQueryService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(ProductCategoryQueryContract::class, $this->service);
    }

    public function testGetTypesForCategoryReturnsEmptyWhenCategoryIsNull(): void
    {
        $this->assertEmptyTypesAndSubtypes($this->service->getTypesForCategory(null, null));
    }

    public function testGetTypesForCategoryReturnsEmptyWhenCategoryIsEmptyString(): void
    {
        $this->assertEmptyTypesAndSubtypes($this->service->getTypesForCategory('', null));
    }

    public function testGetTypesForCategoryReturnsEmptyWhenCategoryNotFound(): void
    {
        $this->categoryRepository->method('findByName')->willReturn(null);

        $this->assertEmptyTypesAndSubtypes($this->service->getTypesForCategory('electronics', null));
    }

    public function testGetTypesForCategoryReturnsTypesWhenCategoryFound(): void
    {
        $type = $this->createMock(Type::class);
        $this->withCategoryFound($this->buildCategoryMock(types: [$type]));

        $result = $this->service->getTypesForCategory('electronics', null);

        $this->assertCount(1, $result['types']);
    }

    public function testGetTypesForCategoryReturnsEmptySubtypesWhenTypeNameIsNull(): void
    {
        $this->withCategoryFound($this->buildCategoryMock(types: []));

        $result = $this->service->getTypesForCategory('electronics', null);

        $this->assertSame([], $result['subtypes']);
    }

    public function testGetTypesForCategoryReturnsEmptySubtypesWhenTypeNotFound(): void
    {
        $this->withCategoryFound($this->buildCategoryMock(types: []));
        $this->typeRepository->method('findByCategoryAndName')->willReturn(null);

        $result = $this->service->getTypesForCategory('electronics', 'smartphones');

        $this->assertSame([], $result['subtypes']);
    }

    public function testGetTypesForCategoryReturnsSubtypesWhenTypeFound(): void
    {
        $subtype = $this->createMock(Subtype::class);
        $type = $this->buildTypeMock(subtypes: [$subtype]);

        $this->withCategoryFound($this->buildCategoryMock(types: [$type]));
        $this->typeRepository->method('findByCategoryAndName')->willReturn($type);

        $result = $this->service->getTypesForCategory('electronics', 'smartphones');

        $this->assertCount(1, $result['subtypes']);
    }

    public function testFindTypeByNameAndCategoryDelegatesToRepository(): void
    {
        $category = $this->createMock(Category::class);
        $type = $this->createMock(Type::class);

        $this->typeRepository
            ->expects($this->once())
            ->method('findByCategoryAndName')
            ->with($category, 'smartphones')
            ->willReturn($type);

        $result = $this->service->findTypeByNameAndCategory($category, 'smartphones');

        $this->assertSame($type, $result);
    }

    public function testGetTypesAndSubtypesMapsTypesToNames(): void
    {
        $type = $this->createMock(Type::class);
        $type->method('getName')->willReturn('Smartphones');

        $this->withCategoryFound($this->buildCategoryMock(types: [$type]));

        $result = $this->service->getTypesAndSubtypes('electronics', null);

        $this->assertContains('Smartphones', $result['types']);
    }

    public function testGetTypesAndSubtypesMapsSubtypesToNames(): void
    {
        $subtype = $this->createMock(Subtype::class);
        $subtype->method('getName')->willReturn('Android');

        $type = $this->buildTypeMock(subtypes: [$subtype]);

        $this->withCategoryFound($this->buildCategoryMock(types: [$type]));
        $this->typeRepository->method('findByCategoryAndName')->willReturn($type);

        $result = $this->service->getTypesAndSubtypes('electronics', 'smartphones');

        $this->assertContains('Android', $result['subtypes']);
    }

    public function testResolveTypesForCategoryReturnsAllTypesWhenNoTypeName(): void
    {
        $type = $this->createMock(Type::class);
        $category = $this->buildCategoryMock(types: [$type]);

        $result = $this->service->resolveTypesForCategory($category, null);

        $this->assertCount(1, $result);
        $this->assertSame($type, $result[0]);
    }

    public function testResolveTypesForCategoryReturnsSingleTypeWhenTypeNameProvided(): void
    {
        $type = $this->createMock(Type::class);
        $category = $this->buildCategoryMock(types: []);

        $this->typeRepository->method('findByCategoryAndName')->willReturn($type);

        $result = $this->service->resolveTypesForCategory($category, 'smartphones');

        $this->assertSame([$type], $result);
    }

    public function testResolveTypesForCategoryReturnsEmptyWhenTypeNotFound(): void
    {
        $category = $this->buildCategoryMock(types: []);
        $this->typeRepository->method('findByCategoryAndName')->willReturn(null);

        $result = $this->service->resolveTypesForCategory($category, 'nonexistent');

        $this->assertSame([], $result);
    }

    private function withCategoryFound(Category $category): void
    {
        $this->categoryRepository->method('findByName')->willReturn($category);
    }

    /**
     * @param array<string, mixed> $result
    */
    private function assertEmptyTypesAndSubtypes(array $result): void
    {
        $this->assertSame([], $result['types']);
        $this->assertSame([], $result['subtypes']);
    }

    /**
     * @param array<int, Type> $types
    */
    private function buildCategoryMock(array $types): Category&MockObject
    {
        $category = $this->createMock(Category::class);
        $category->method('getTypes')->willReturn(new ArrayCollection($types));

        return $category;
    }

    /**
     * @param array<int, Subtype> $subtypes
    */
    private function buildTypeMock(array $subtypes): Type&MockObject
    {
        $type = $this->createMock(Type::class);
        $type->method('getSubtypes')->willReturn(new ArrayCollection($subtypes));

        return $type;
    }

    private function initMocks(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepositoryContract::class);
        $this->typeRepository = $this->createMock(TypeRepositoryContract::class);
    }

    private function initService(): void
    {
        $this->service = new ProductCategoryQueryService(
            $this->categoryRepository,
            $this->typeRepository,
        );
    }
}
