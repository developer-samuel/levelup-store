<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Service\Query;

use Kit\Utils\Shared\StringNormalizer;

use App\Core\Domain\Segment\Product\Enum\ProductTitleType;

use App\Core\Ports\Segment\Product\Service\Query\ProductTitleQueryContract;

final class ProductTitleQueryService implements ProductTitleQueryContract
{
    /**
     * @param string|null $category
     * @param string|null $type
     * @param bool $isDiscountRoute
     *
     * @return string
    */
    public function generateTitle(?string $category, ?string $type, bool $isDiscountRoute): string
    {
        return match ($this->getCategoryTypeState($category, $type)) {
            ProductTitleType::EMPTY         => $this->generateDefaultTitle($isDiscountRoute),
            ProductTitleType::CATEGORY_ONLY => $this->generateTextTitle($category ?? '', $isDiscountRoute),
            ProductTitleType::BOTH          => $this->generateTextTitle($type ?? '', $isDiscountRoute),
        };
    }

    /**
     * @param bool $isDiscountRoute
     *
     * @return string
    */
    private function generateDefaultTitle(bool $isDiscountRoute): string
    {
        return $isDiscountRoute ? 'Discounts' : 'Products';
    }

    /**
     * @param string $text
     * @param bool $isDiscountRoute
     *
     * @return string
    */
    private function generateTextTitle(string $text, bool $isDiscountRoute): string
    {
        $formatted = $this->formatText($text);
        return $isDiscountRoute ? 'Discounted: ' . $formatted : $formatted;
    }

    /**
     * @param string $text
     *
     * @return string
    */
    private function formatText(string $text): string
    {
        $length = mb_strlen($text);

        return $length <= 2
            ? StringNormalizer::toUpperCase($text)
            : StringNormalizer::capitalizeWords($text);
    }

    /**
     * @param string|null $category
     * @param string|null $type
     *
     * @return ProductTitleType
    */
    private function getCategoryTypeState(?string $category, ?string $type): ProductTitleType
    {
        $hasCategory = !empty($category);
        $hasType = !empty($type);

        return $hasCategory
            ? ($hasType ? ProductTitleType::BOTH : ProductTitleType::CATEGORY_ONLY)
            : ProductTitleType::EMPTY;
    }
}
