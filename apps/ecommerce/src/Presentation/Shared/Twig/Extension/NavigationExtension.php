<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Twig\Extension;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};

use Doctrine\Common\Collections\Collection;

use Kit\Utils\Shared\DataSanitizer;

final class NavigationExtension extends AbstractExtension
{
    private const MAX_TYPES = 6;

    /**
     * @return TwigFunction[]
    */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'getTypesToShow',
                fn(mixed $types, mixed $max = self::MAX_TYPES): array => $this->sliceTypes($types, $max),
            ),
            new TwigFunction(
                'getTypeCount',
                fn(mixed $types, mixed $max = self::MAX_TYPES): int => count($this->sliceTypes($types, $max)),
            ),
        ];
    }

    /**
     * @param mixed $types
     * @param mixed $max
     *
     * @return array<int, string>
    */
    private function sliceTypes(mixed $types, mixed $max): array
    {
        $typesArray = $this->normalizeTypes($types);
        $normalizedMax = $this->normalizeMax($max);
        return array_slice($typesArray, 0, $normalizedMax);
    }

    /**
     * @param mixed $types
     *
     * @return array<int, string>
    */
    private function normalizeTypes(mixed $types): array
    {
        if ($types instanceof Collection) {
            /** @var array<int, string> $array */
            $array = $types->toArray();
            return $array;
        }

        return is_array($types) ?
            array_values(array_filter(
                $types,
                static fn($t): bool => is_string($t),
            )) : [];
    }

    /**
     * @param mixed $max
     *
     * @return int
    */
    private function normalizeMax(mixed $max): int
    {
        $sanitized = DataSanitizer::sanitizeInt($max);

        return $sanitized ?? self::MAX_TYPES;
    }
}
