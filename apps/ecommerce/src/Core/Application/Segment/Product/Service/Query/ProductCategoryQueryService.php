<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Service\Query;

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Type\Entity\Type,
    Segment\Subtype\Entity\Subtype
};

use App\Core\Ports\{
    Segment\Category\Repository\CategoryRepositoryContract,
    Segment\Product\Service\Query\ProductCategoryQueryContract,
    Segment\Type\TypeRepositoryContract
};

/**
 * @phpstan-import-type TypesAndSubtypes from ProductCategoryQueryContract
 * @phpstan-import-type TypesAndSubtypesNames from ProductCategoryQueryContract
*/
final readonly class ProductCategoryQueryService implements ProductCategoryQueryContract
{
    public function __construct(
        private CategoryRepositoryContract $categoryRepository,
        private TypeRepositoryContract $typeRepository,
    ) {}

    /** @return TypesAndSubtypes */
    public function getTypesForCategory(?string $categoryName, ?string $typeName): array
    {
        if (!$this->isValidCategoryName($categoryName)) {
            return $this->emptyTypesAndSubtypes();
        }

        $category = $this->getCategoryByName($categoryName ?? '');

        if ($category === null) {
            return $this->emptyTypesAndSubtypes();
        }

        $types = $this->getTypesFromCategory($category);
        $subtypes = $this->getSubtypesForTypeName($category, $typeName);

        return [
            'types'    => $types,
            'subtypes' => $subtypes,
        ];
    }

    public function findTypeByNameAndCategory(Category $category, string $typeName): ?Type
    {
        return $this->typeRepository->findByCategoryAndName($category, $typeName);
    }

    public function getTypesAndSubtypes(?string $category, ?string $type): array
    {
        $entities = $this->getTypesForCategory($category, $type);

        return [
            'types'    => $this->mapEntitiesToNames($entities['types']),
            'subtypes' => $this->mapEntitiesToNames($entities['subtypes']),
        ];
    }

    /** @return Type[] */
    public function resolveTypesForCategory(Category $category, ?string $typeName): array
    {
        if ($typeName !== null) {
            $typeEntity = $this->findTypeByNameAndCategory($category, $typeName);
            return $typeEntity !== null ? [$typeEntity] : [];
        }

        return $category->getTypes()->toArray();
    }

    private function isValidCategoryName(?string $categoryName): bool
    {
        return $categoryName !== null && trim($categoryName) !== '';
    }

    /** @return TypesAndSubtypes */
    private function emptyTypesAndSubtypes(): array
    {
        return [
            'types'    => [],
            'subtypes' => [],
        ];
    }

    private function getCategoryByName(string $categoryName): ?Category
    {
        return $this->categoryRepository->findByName($categoryName);
    }

    /** @return Type[] */
    private function getTypesFromCategory(Category $category): array
    {
        return $category->getTypes()->toArray();
    }

    /** @return Subtype[] */
    private function getSubtypesForTypeName(Category $category, ?string $typeName): array
    {
        if ($typeName === null) {
            return [];
        }

        $type = $this->findTypeByNameAndCategory($category, $typeName);
        if ($type === null) {
            return [];
        }

        $subtypes = $type->getSubtypes()->toArray();

        return $this->filterSubtypes($subtypes);
    }

    /**
     * @param Type[]|Subtype[] $entities
     *
     * @return string[]
    */
    private function mapEntitiesToNames(array $entities): array
    {
        return array_map(
            static fn(Type|Subtype $e): string => $e->getName(),
            $entities,
        );
    }

    /**
     * @param array<mixed> $items
     *
     * @return Subtype[]
    */
    private function filterSubtypes(array $items): array
    {
        $subtypes = array_filter(
            $items,
            static fn($item): bool => $item instanceof Subtype,
        );

        return array_values($subtypes);
    }
}
