<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Product\Handler\Query;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\ValueObject\ProductFilterObject
};

use App\Core\Application\Segment\Product\Handler\Query\ProductQueryHandler;

use App\Core\Ports\{
    Segment\Product\Handler\Query\ProductQueryHandlerContract,
    Segment\Product\Service\Query\ProductQueryContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Product\Handler\Query\ProductQueryHandler
*/
final class ProductQueryHandlerTest extends TestCase
{
    private ProductQueryContract&MockObject $productQuery;
    private ReviewQueryContract&MockObject $reviewQuery;
    private ProductQueryHandler $handler;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initHandler();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(ProductQueryHandlerContract::class, $this->handler);
    }

    public function testHandleReturnsArray(): void
    {
        $this->withEmptyProductData();

        $result = $this->handler->handle($this->buildFilter());

        $this->assertNotEmpty($result);
    }

    public function testHandleDelegatesToProductQuery(): void
    {
        $filter = $this->buildFilter();

        $this->productQuery
            ->expects($this->once())
            ->method('getFilteredAndSortedData')
            ->with($filter, 1, ProductSortOption::TOP_RATED)
            ->willReturn([]);

        $this->handler->handle($filter);
    }

    public function testHandleAddsAverageRatingToProducts(): void
    {
        $this->productQuery->method('getFilteredAndSortedData')->willReturn([
            'products' => [
                ['variantId' => 42, 'name' => 'Test'],
            ],
        ]);

        $this->reviewQuery
            ->expects($this->once())
            ->method('getAverageRatingByVariant')
            ->with(42)
            ->willReturn(4.5);

        /** @var array{products: array<int, array<string, mixed>>} $result */
        $result = $this->handler->handle($this->buildFilter());

        $this->assertSame(4.5, $result['products'][0]['averageRating']);
    }

    public function testHandleReturnsZeroRatingWhenVariantIdMissing(): void
    {
        $this->productQuery->method('getFilteredAndSortedData')->willReturn([
            'products' => [
                ['name' => 'No Variant Id'],
            ],
        ]);

        $this->reviewQuery->expects($this->never())->method('getAverageRatingByVariant');

        /** @var array{products: array<int, array<string, mixed>>} $result */
        $result = $this->handler->handle($this->buildFilter());

        $this->assertSame(0.0, $result['products'][0]['averageRating']);
    }

    public function testHandleFiltersOutNonArrayProducts(): void
    {
        $this->productQuery->method('getFilteredAndSortedData')->willReturn([
            'products' => [
                ['variantId' => 1, 'name' => 'Valid'],
                'not_an_array',
                42,
            ],
        ]);

        $this->reviewQuery->method('getAverageRatingByVariant')->willReturn(0.0);

        /** @var array{products: array<int, mixed>} $result */
        $result = $this->handler->handle($this->buildFilter());

        $this->assertCount(1, $result['products']);
    }

    public function testHandleReturnsEmptyProductsWhenNotArray(): void
    {
        $this->productQuery->method('getFilteredAndSortedData')->willReturn([
            'products' => 'invalid',
        ]);

        $result = $this->handler->handle($this->buildFilter());

        $this->assertSame([], $result['products']);
    }

    public function testHandleSanitizesShowLoadMoreFromPagination(): void
    {
        $this->productQuery->method('getFilteredAndSortedData')->willReturn([
            'showLoadMore' => false,
            'pagination'   => [
                'maxPages'     => 3,
                'currentPage'  => 1,
                'totalCount'   => 36,
                'showLoadMore' => true,
            ],
        ]);

        $result = $this->handler->handle($this->buildFilter());

        $this->assertTrue($result['showLoadMore']);
    }

    public function testHandleDefaultsShowLoadMoreToFalseWhenNoPagination(): void
    {
        $this->withEmptyProductData();

        $result = $this->handler->handle($this->buildFilter());

        $this->assertFalse($result['showLoadMore']);
    }

    public function testHandlePassesCustomPageAndSort(): void
    {
        $filter = $this->buildFilter();
        $sort = ProductSortOption::CHEAPEST;

        $this->productQuery
            ->expects($this->once())
            ->method('getFilteredAndSortedData')
            ->with($filter, 3, $sort)
            ->willReturn([]);

        $this->handler->handle($filter, 3, $sort);
    }

    private function initMocks(): void
    {
        $this->productQuery = $this->createMock(ProductQueryContract::class);
        $this->reviewQuery = $this->createMock(ReviewQueryContract::class);
    }

    private function initHandler(): void
    {
        $this->handler = new ProductQueryHandler(
            $this->productQuery,
            $this->reviewQuery,
        );
    }

    private function withEmptyProductData(): void
    {
        $this->productQuery->method('getFilteredAndSortedData')->willReturn([]);
    }

    private function buildFilter(): ProductFilterObject
    {
        return new ProductFilterObject(
            isDiscountRoute: false,
            subtypes:        [],
            brands:          [],
        );
    }
}
