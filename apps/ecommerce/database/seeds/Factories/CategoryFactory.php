<?php

declare(strict_types=1);

namespace Database\Seeds\Factories;

use App\Core\Domain\Segment\Category\Entity\Category;

trait CategoryFactory
{
    /**
     * @param string $name
     *
     * @return Category
    */
    private function createCategory(string $name): Category
    {
        return (new Category())
            ->setName($name);
    }
}
