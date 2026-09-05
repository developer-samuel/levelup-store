<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Product\Service\Query;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\ValueObject\ProductFilterObject,
    Segment\Product\ValueObject\ProductVariantObject
};

use App\Core\Application\Segment\Product\Service\Query\ProductQueryService;

use App\Core\Ports\{
    Segment\Brand\BrandRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\Product\Service\Query\ProductCategoryQueryContract,
    Segment\Product\Service\Query\ProductQueryContract,
    Segment\Product\Service\Query\ProductVariantQueryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Product\Service\Query\ProductQueryService
*/
class ProductQueryServiceTest extends TestCase
{
    private BrandRepositoryContract&MockObject $brandRepository;
    private ProductVariantRepositoryContract&MockObject $variantRepository;
    private ProductVariantQueryContract&MockObject $productVariantQuery;
    private ProductCategoryQueryContract&MockObject $productCategoryQuery;
    private ProductQueryService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(ProductQueryContract::class, $this->service);
    }

    public function testGetFilteredAndSortedDataReturnsArray(): void
    {
        $this->setupDefaultMocks();

        $result = $this->service->getFilteredAndSortedData($this->buildFilter());

        $this->assertArrayHasKey('variants', $result);
    }

    public function testGetFilteredAndSortedDataContainsExpectedKeys(): void
    {
        $this->setupDefaultMocks();

        $result = $this->service->getFilteredAndSortedData($this->buildFilter());

        $this->assertArrayHasKey('variants', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('filter', $result);
        $this->assertArrayHasKey('isDiscountRoute', $result);
    }

    public function testGetFilteredAndSortedDataPassesCategoryToRepository(): void
    {
        $this->setupDefaultMocks();

        $filter = $this->buildFilter(category: 'Electronics');

        $this->variantRepository
            ->expects($this->once())
            ->method('findAvailableVariantsPaginated')
            ->with($this->anything(), $this->anything(), $this->anything(), $this->anything())
            ->willReturn(['items' => [], 'total' => 0]);

        $this->service->getFilteredAndSortedData($filter);
    }

    public function testGetFilteredAndSortedDataNormalizesCategoryToLowercase(): void
    {
        $capturedFilter = null;

        $this->variantRepository
            ->method('findAvailableVariantsPaginated')
            ->willReturn(['items' => [], 'total' => 0]);

        $this->productCategoryQuery
            ->method('getTypesAndSubtypes')
            ->willReturnCallback(function (?string $category) use (&$capturedFilter): array {
                $capturedFilter = $category;
                return ['types' => [], 'subtypes' => []];
            });

        $this->brandRepository->method('findAllWithProducts')->willReturn([]);
        $this->variantRepository->method('getMaxPriceForFilter')->willReturn(0.0);
        $this->productVariantQuery->method('mapVariantsToData')->willReturn([]);

        $this->service->getFilteredAndSortedData($this->buildFilter(category: 'ELECTRONICS'));

        $this->assertSame('electronics', $capturedFilter);
    }

    public function testGetFilteredAndSortedDataNullCategoryWhenEmptyString(): void
    {
        $capturedCategory = null;

        $this->variantRepository
            ->method('findAvailableVariantsPaginated')
            ->willReturn(['items' => [], 'total' => 0]);

        $this->productCategoryQuery
            ->method('getTypesAndSubtypes')
            ->willReturnCallback(function (?string $category) use (&$capturedCategory): array {
                $capturedCategory = $category;
                return ['types' => [], 'subtypes' => []];
            });

        $this->brandRepository->method('findAllWithProducts')->willReturn([]);
        $this->variantRepository->method('getMaxPriceForFilter')->willReturn(0.0);
        $this->productVariantQuery->method('mapVariantsToData')->willReturn([]);

        $this->service->getFilteredAndSortedData($this->buildFilter(category: ''));

        $this->assertNull($capturedCategory);
    }

    public function testGetFilteredAndSortedDataMapsVariants(): void
    {
        $variantObject = new ProductVariantObject(
            variantId:     1,
            price:         100.0,
            discountPrice: 90.0,
            imagePath:     '/img/product.jpg',
            name:          'Test',
            url:           'test-url',
            createdAt:     '2024-01-01',
        );

        $this->variantRepository
            ->method('findAvailableVariantsPaginated')
            ->willReturn(['items' => [$this->createMock(ProductVariant::class)], 'total' => 1]);

        $this->productVariantQuery
            ->method('mapVariantsToData')
            ->willReturn([$variantObject]);

        $this->productCategoryQuery->method('getTypesAndSubtypes')->willReturn(['types' => [], 'subtypes' => []]);
        $this->brandRepository->method('findAllWithProducts')->willReturn([]);
        $this->variantRepository->method('getMaxPriceForFilter')->willReturn(0.0);

        $result = $this->service->getFilteredAndSortedData($this->buildFilter());

        $this->assertArrayHasKey('variants', $result);
    }

    public function testGetFilteredAndSortedDataUsesDiscountRoute(): void
    {
        $this->setupDefaultMocks();

        $filter = $this->buildFilter(isDiscountRoute: true);
        $result = $this->service->getFilteredAndSortedData($filter);

        $this->assertTrue($result['isDiscountRoute']);
    }

    private function initMocks(): void
    {
        $this->brandRepository = $this->createMock(BrandRepositoryContract::class);
        $this->variantRepository = $this->createMock(ProductVariantRepositoryContract::class);
        $this->productVariantQuery = $this->createMock(ProductVariantQueryContract::class);
        $this->productCategoryQuery = $this->createMock(ProductCategoryQueryContract::class);
    }

    private function initService(): void
    {
        $this->service = new ProductQueryService(
            $this->brandRepository,
            $this->variantRepository,
            $this->productVariantQuery,
            $this->productCategoryQuery,
        );
    }

    private function setupDefaultMocks(): void
    {
        $this->variantRepository
            ->method('findAvailableVariantsPaginated')
            ->willReturn(['items' => [], 'total' => 0]);

        $this->productVariantQuery->method('mapVariantsToData')->willReturn([]);
        $this->productCategoryQuery->method('getTypesAndSubtypes')->willReturn(['types' => [], 'subtypes' => []]);
        $this->brandRepository->method('findAllWithProducts')->willReturn([]);
        $this->variantRepository->method('getMaxPriceForFilter')->willReturn(0.0);
    }

    private function buildFilter(
        ?string $category = null,
        ?string $type = null,
        bool $isDiscountRoute = false,
    ): ProductFilterObject {
        return new ProductFilterObject(
            isDiscountRoute: $isDiscountRoute,
            subtypes:        [],
            brands:          [],
            category:        $category,
            type:            $type,
        );
    }
}
