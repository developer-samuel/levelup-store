<?php

declare(strict_types=1);

namespace App\Core\Domain\Home;

final readonly class HomeCacheObject
{
    /**
     * @param array<int, mixed> $products
     * @param array<int, mixed> $banners
    */
    public function __construct(
        public array $products,
        public array $banners,
    ) {}

    /**
     * @return array<string, mixed>
    */
    public function toArray(): array
    {
        return [
            'products' => $this->products,
            'banners'  => $this->banners,
        ];
    }
}
