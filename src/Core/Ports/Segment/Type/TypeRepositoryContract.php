<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Type;

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Type\Entity\Type
};

interface TypeRepositoryContract
{
    /**
     * @param string $name
     *
     * @return Type|null
    */
    public function findByName(string $name): ?Type;

    /**
     * @param Category $category
     * @param string $name
     *
     * @return Type|null
    */
    public function findByCategoryAndName(Category $category, string $name): ?Type;
}
