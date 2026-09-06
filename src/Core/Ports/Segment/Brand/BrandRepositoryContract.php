<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Brand;

use App\Core\Domain\Segment\Brand\Brand;

interface BrandRepositoryContract
{
    /**
     * @return Brand[]
    */
    public function findAll(): array;

    /**
     * @param string|null $category
     * @param string|null $type
     *
     * @return Brand[]
    */
    public function findAllWithProducts(?string $category = null, ?string $type = null): array;

     /**
     * @param int $id
     *
     * @return Brand|null
    */
    public function findById(int $id): ?Brand;

    /**
     * @param string $name
     *
     * @return Brand|null
    */
    public function findByName(string $name): ?Brand;

    /**
     * @param string $name
     *
     * @return bool
    */
    public function existsByName(string $name): bool;
}
