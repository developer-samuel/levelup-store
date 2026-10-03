<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Brand;

use App\Core\Domain\Segment\Brand\Brand;

interface BrandRepositoryContract
{
    /** @return Brand[] */
    public function findAll(): array;

    /** @return Brand[] */
    public function findAllWithProducts(?string $category = null, ?string $type = null): array;

    public function findById(int $id): ?Brand;
    public function findByName(string $name): ?Brand;
    public function existsByName(string $name): bool;
}
