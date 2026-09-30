<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Category;

use App\Core\Domain\Segment\{
    Category\Entity\Category,
    Subtype\Entity\Subtype,
    Type\Entity\Type
};

/**
 * @phpstan-type SubtypeArray array{name: string}
 * @phpstan-type TypeArray array{name: string, subtypes: list<string>}
 * @phpstan-type ResourceArray array{name: string, image: string, types: list<TypeArray>}
*/
final class CategoryResource
{
    private const IMAGE_BASE_PATH = '/img/icons/categories/';

    /**
     * @param Category $category
     *
     * @return ResourceArray
    */
    public static function toArray(Category $category): array
    {
        return [
            'name'  => $category->getName(),
            'image' => self::IMAGE_BASE_PATH . strtolower($category->getName()) . '.png',
            'types' => array_values(
                $category->getTypes()->map(
                    static fn(Type $type): array => [
                        'name'     => $type->getName(),
                        'subtypes' => array_values(
                            $type->getSubtypes()->map(
                                static fn(Subtype $subtype): string => $subtype->getName(),
                            )->toArray(),
                        ),
                    ],
                )->toArray(),
            ),
        ];
    }
}
