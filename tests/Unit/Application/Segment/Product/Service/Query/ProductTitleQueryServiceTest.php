<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Product\Service\Query;

use PHPUnit\Framework\TestCase;

use App\Core\Application\Segment\Product\Service\Query\ProductTitleQueryService;

use App\Core\Ports\Segment\Product\Service\Query\ProductTitleQueryContract;

/**
 * @coversDefaultClass \App\Core\Application\Segment\Product\Service\Query\ProductTitleQueryService
*/
final class ProductTitleQueryServiceTest extends TestCase
{
    private ProductTitleQueryService $service;

    protected function setUp(): void
    {
        $this->service = new ProductTitleQueryService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(ProductTitleQueryContract::class, $this->service);
    }

    public function testGenerateTitleReturnsProductsWhenNoFilters(): void
    {
        $this->assertSame('Products', $this->service->generateTitle(null, null, false));
    }

    public function testGenerateTitleReturnsDiscountsWhenNoFiltersAndDiscountRoute(): void
    {
        $this->assertSame('Discounts', $this->service->generateTitle(null, null, true));
    }

    public function testGenerateTitleReturnsCategoryTitleWhenOnlyCategoryProvided(): void
    {
        $result = $this->service->generateTitle('electronics', null, false);

        $this->assertSame('Electronics', $result);
    }

    public function testGenerateTitleReturnsCategoryTitleWithDiscountPrefixWhenDiscountRoute(): void
    {
        $result = $this->service->generateTitle('electronics', null, true);

        $this->assertSame('Discounted: Electronics', $result);
    }

    public function testGenerateTitleReturnsTypeTitleWhenBothCategoryAndTypeProvided(): void
    {
        $result = $this->service->generateTitle('electronics', 'smartphones', false);

        $this->assertSame('Smartphones', $result);
    }

    public function testGenerateTitleReturnsTypeTitleWithDiscountPrefixWhenBothAndDiscountRoute(): void
    {
        $result = $this->service->generateTitle('electronics', 'smartphones', true);

        $this->assertSame('Discounted: Smartphones', $result);
    }

    public function testGenerateTitleUpperCasesShortTextOfOneChar(): void
    {
        $result = $this->service->generateTitle('a', null, false);

        $this->assertSame('A', $result);
    }

    public function testGenerateTitleUpperCasesShortTextOfTwoChars(): void
    {
        $result = $this->service->generateTitle('tv', null, false);

        $this->assertSame('TV', $result);
    }

    public function testGenerateTitleCapitalizesWordsForLongerText(): void
    {
        $result = $this->service->generateTitle('running shoes', null, false);

        $this->assertSame('Running Shoes', $result);
    }

    public function testGenerateTitleReturnsProductsWhenCategoryIsEmptyString(): void
    {
        $this->assertSame('Products', $this->service->generateTitle('', null, false));
    }

    public function testGenerateTitleIgnoresTypeWhenCategoryIsNull(): void
    {
        $this->assertSame('Products', $this->service->generateTitle(null, 'smartphones', false));
    }
}
