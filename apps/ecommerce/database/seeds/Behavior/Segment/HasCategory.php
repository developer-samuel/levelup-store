<?php

declare(strict_types=1);

namespace Database\Seeds\Behavior\Segment;

use App\Core\Domain\Segment\Category\Entity\Category;

trait HasCategory
{
    private function getCategory(string $name): ?Category
    {
        return $this->categoryRepository->findByName($name);
    }

    private function findCategoryOrLog(string $name): ?Category
    {
        $category = $this->getCategory($name);
        if ($category === null) {
            $this->consoleLogger->logError(sprintf("Required Category '%s' not found in database.", $name));
        }

        return $category;
    }
}
