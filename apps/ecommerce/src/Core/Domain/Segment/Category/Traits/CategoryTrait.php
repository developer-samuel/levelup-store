<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Category\Traits;

use App\Core\Domain\Segment\Category\Entity\Category;

/**
 * @property Category $category
*/
trait CategoryTrait
{
    /**
     * @return Category
    */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     *
     * @return self
    */
    public function setCategory(Category $category): self
    {
        $this->category = $category;
        return $this;
    }
}
