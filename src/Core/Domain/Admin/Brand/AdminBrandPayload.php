<?php

declare(strict_types=1);

namespace App\Core\Domain\Admin\Brand;

final readonly class AdminBrandPayload
{
    /**
     * @param string $name
     * @param string|null $id
    */
    public function __construct(
        public string $name,
        public ?string $id = null,
    ) {}
}
