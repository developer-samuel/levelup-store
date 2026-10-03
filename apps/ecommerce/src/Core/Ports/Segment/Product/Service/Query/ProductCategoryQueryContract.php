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
    /** @return TypesAndSubtypes */
    public function getTypesForCategory(?string $categoryName, ?string $typeName): array;

    public function findTypeByNameAndCategory(Category $category, string $typeName): ?Type;

    /** @return TypesAndSubtypesNames */
    public function getTypesAndSubtypes(?string $category, ?string $type): array;

    /** @return Type[] */
    public function resolveTypesForCategory(Category $categoryEntity, ?string $type): array;
}
