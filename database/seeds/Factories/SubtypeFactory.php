<?php

declare(strict_types=1);

namespace Database\Seeds\Factories;

use Doctrine\Persistence\ObjectManager;

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Subtype\Entity\Subtype,
    Segment\Type\Entity\Type
};

use App\Core\Ports\Segment\Type\TypeRepositoryContract;

trait SubtypeFactory
{
    /**
     * @param TypeRepositoryContract $typeRepository
     * @param Category $category
     * @param string $name
     *
     * @return Type|null
    */
    private function getType(
        TypeRepositoryContract $typeRepository,
        Category $category,
        string $name,
    ): ?Type {
        return $typeRepository->findByCategoryAndName($category, $name);
    }

    /**
     * @param ObjectManager $manager
     * @param Category $category
     * @param Type $type
     * @param string[] $subtypes
     *
     * @return void
    */
    private function createSubtypes(ObjectManager $manager, Category $category, Type $type, array $subtypes): void
    {
        foreach ($subtypes as $subtypeName) {
            $subtype = (new Subtype())
                ->setCategory($category)
                ->setType($type)
                ->setName($subtypeName);

            $manager->persist($subtype);
        }
    }
}
