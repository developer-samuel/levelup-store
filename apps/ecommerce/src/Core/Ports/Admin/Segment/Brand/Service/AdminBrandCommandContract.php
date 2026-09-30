<?php

declare(strict_types=1);

namespace App\Core\Ports\Admin\Segment\Brand\Service;

use App\Core\Domain\{
    Admin\Brand\AdminBrandPayload,
    Segment\Brand\Brand
};

interface AdminBrandCommandContract
{
    /**
     * @param AdminBrandPayload $payload
     *
     * @return Brand
    */
    public function createBrand(AdminBrandPayload $payload): Brand;

    /**
     * @param int $id
     * @param AdminBrandPayload $payload
     *
     * @return Brand
    */
    public function updateBrand(int $id, AdminBrandPayload $payload): Brand;

    /**
     * @param Brand $brand
     *
     * @return void
    */
    public function destroyBrand(Brand $brand): void;

    /**
     * @param AdminBrandPayload $payload
     *
     * @return int
     *
     * @throws \InvalidArgumentException
    */
    public function validateId(AdminBrandPayload $payload): int;
}
