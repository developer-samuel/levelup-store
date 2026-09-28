<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Subtype\Entity\Subtype,
    Segment\Type\Entity\Type
};

/**
 * @phpstan-type TypesAndSubtypes array{
 *     types: Type[],
 *     subtypes: Subtype[]
 * }
 * @phpstan-type TypesAndSubtypesNames array{
 *     types: string[],
 *     subtypes: string[]
 * }
*/
interface ProductCategoryQueryContract
{
    /**
     * @param string|null $categoryName
     * @param string|null $typeName
     *
     * @return TypesAndSubtypes
    */
    public function getTypesForCategory(?string $categoryName, ?string $typeName): array;

    /**
     * @param Category $category
     * @param string $typeName
     *
     * @return Type|null
    */
    public function findTypeByNameAndCategory(Category $category, string $typeName): ?Type;

    /**
     * @param string|null $category
     * @param string|null $type
     *
     * @return TypesAndSubtypesNames
    */
    public function getTypesAndSubtypes(?string $category, ?string $type): array;

    /**
     * @param Category $categoryEntity
     * @param string|null $type
     *
     * @return Type[]
    */
    public function resolveTypesForCategory(Category $categoryEntity, ?string $type): array;
}
