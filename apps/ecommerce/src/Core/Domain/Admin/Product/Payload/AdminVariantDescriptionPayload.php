<?php

declare(strict_types=1);

namespace App\Core\Domain\Admin\Product\Payload;

final readonly class AdminVariantDescriptionPayload
{
    /**
     * @param int $position
     * @param string $title
     * @param string $body
     * @param string|null $variantId
     * @param string|null $id
    */
    public function __construct(
        public int $position,
        public string $title,
        public string $body,
        public ?string $variantId = null,
        public ?string $id = null,
    ) {}
}
