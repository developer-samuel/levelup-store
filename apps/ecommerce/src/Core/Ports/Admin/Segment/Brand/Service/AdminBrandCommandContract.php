<?php

declare(strict_types=1);

namespace App\Core\Ports\Admin\Segment\Brand\Service;

use App\Core\Domain\{
    Admin\Brand\AdminBrandPayload,
    Segment\Brand\Brand
};

interface AdminBrandCommandContract
{
    public function createBrand(AdminBrandPayload $payload): Brand;
    public function updateBrand(int $id, AdminBrandPayload $payload): Brand;
    public function destroyBrand(Brand $brand): void;
    public function validateId(AdminBrandPayload $payload): int;
}
