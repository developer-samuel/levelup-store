<?php

declare(strict_types=1);

namespace Database\Seeds\Factories;

use Doctrine\Persistence\ObjectManager;

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Type\Entity\Type
};

trait TypeFactory
{
    /** @param string[] $types */
    private function createAndPersistTypes(
        ObjectManager $manager,
        Category $category,
        array $types,
    ): void {
        foreach ($types as $typeName) {
            $type = $this->createType($category, $typeName);

            $manager->persist($type);
        }
    }

    private function createType(Category $category, string $name): Type
    {
        return (new Type())
            ->setCategory($category)
            ->setName($name);
    }
}
