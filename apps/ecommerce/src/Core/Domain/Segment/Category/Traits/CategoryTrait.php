<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Category\Traits;

use App\Core\Domain\Segment\Category\Entity\Category;

trait CategoryTrait
{
    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;
        return $this;
    }
}
