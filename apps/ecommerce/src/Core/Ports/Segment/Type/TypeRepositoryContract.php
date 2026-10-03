<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Type;

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Type\Entity\Type
};

interface TypeRepositoryContract
{
    public function findByName(string $name): ?Type;
    public function findByCategoryAndName(Category $category, string $name): ?Type;
}
