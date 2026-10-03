<?php

declare(strict_types=1);

namespace App\Core\Domain\Admin\Product\Payload;

final readonly class AdminVariantEanPayload
{
    public function __construct(
        public string $code,
        public ?string $variantId = null,
        public ?string $id = null,
    ) {}
}
