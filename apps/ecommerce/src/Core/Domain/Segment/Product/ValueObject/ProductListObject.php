<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

final readonly class ProductListObject
{
    /**
     * @param ProductVariant[] $variants
     * @param int $maxPages
     * @param int $currentPage
     * @param string $sort
     * @param int $totalCount
     * @param bool $showLoadMore
    */
    public function __construct(
        public array $variants,
        public int $maxPages,
        public int $currentPage,
        public string $sort,
        public int $totalCount,
        public bool $showLoadMore,
    ) {}

    /**
     * @return array{
     *     variants: ProductVariant[],
     *     maxPages: int,
     *     currentPage: int,
     *     sort: string,
     *     totalCount: int,
     *     showLoadMore: bool
     * }
    */
    public function toArray(): array
    {
        return [
            'variants'     => $this->variants,
            'maxPages'     => $this->maxPages,
            'currentPage'  => $this->currentPage,
            'sort'         => $this->sort,
            'totalCount'   => $this->totalCount,
            'showLoadMore' => $this->showLoadMore,
        ];
    }
}
