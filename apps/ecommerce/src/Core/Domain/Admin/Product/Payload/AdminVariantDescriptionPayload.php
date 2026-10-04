<?php

declare(strict_types=1);

namespace App\Core\Domain\Admin\Product\Payload;

final readonly class AdminVariantDescriptionPayload
{
    public function __construct(
        public int $position,
        public string $title,
        public string $body,
        public ?string $variantId = null,
        public ?string $id = null,
    ) {}
}
