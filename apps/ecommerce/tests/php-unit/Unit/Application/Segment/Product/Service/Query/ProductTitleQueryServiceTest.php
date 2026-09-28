<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Product\Service\Query;

use PHPUnit\Framework\TestCase;

use App\Core\Application\Segment\Product\Service\Query\ProductTitleQueryService;

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

    public function testGenerateTitleReturnsProductsWhenNoFilters(): void
    {
        self::assertSame('Products', $this->service->generateTitle(null, null, false));
    }

    public function testGenerateTitleReturnsDiscountsWhenNoFiltersAndDiscountRoute(): void
    {
        self::assertSame('Discounts', $this->service->generateTitle(null, null, true));
    }

    public function testGenerateTitleReturnsCategoryTitleWhenOnlyCategoryProvided(): void
    {
        $result = $this->service->generateTitle('electronics', null, false);

        self::assertSame('Electronics', $result);
    }

    public function testGenerateTitleReturnsCategoryTitleWithDiscountPrefixWhenDiscountRoute(): void
    {
        $result = $this->service->generateTitle('electronics', null, true);

        self::assertSame('Discounted: Electronics', $result);
    }

    public function testGenerateTitleReturnsTypeTitleWhenBothCategoryAndTypeProvided(): void
    {
        $result = $this->service->generateTitle('electronics', 'smartphones', false);

        self::assertSame('Smartphones', $result);
    }

    public function testGenerateTitleReturnsTypeTitleWithDiscountPrefixWhenBothAndDiscountRoute(): void
    {
        $result = $this->service->generateTitle('electronics', 'smartphones', true);

        self::assertSame('Discounted: Smartphones', $result);
    }

    public function testGenerateTitleUpperCasesShortTextOfOneChar(): void
    {
        $result = $this->service->generateTitle('a', null, false);

        self::assertSame('A', $result);
    }

    public function testGenerateTitleUpperCasesShortTextOfTwoChars(): void
    {
        $result = $this->service->generateTitle('tv', null, false);

        self::assertSame('TV', $result);
    }

    public function testGenerateTitleCapitalizesWordsForLongerText(): void
    {
        $result = $this->service->generateTitle('running shoes', null, false);

        self::assertSame('Running Shoes', $result);
    }

    public function testGenerateTitleReturnsProductsWhenCategoryIsEmptyString(): void
    {
        self::assertSame('Products', $this->service->generateTitle('', null, false));
    }

    public function testGenerateTitleIgnoresTypeWhenCategoryIsNull(): void
    {
        self::assertSame('Products', $this->service->generateTitle(null, 'smartphones', false));
    }
}
