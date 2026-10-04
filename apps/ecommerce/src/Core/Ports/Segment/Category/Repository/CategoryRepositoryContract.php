<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Category\Repository;

use App\Core\Domain\Segment\Category\Entity\Category;

interface CategoryRepositoryContract
{
    /** @return Category[] */
    public function findAll(): array;

    public function findByName(string $name): ?Category;
}
