<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Twig\Extension;

use Twig\{
    Extension\AbstractExtension,
    TwigFilter
};

use Presets\Kit\Utils\Shared\StringNormalizer;

final class FilterExtension extends AbstractExtension
{
    /** @return TwigFilter[] */
    public function getFilters(): array
    {
        return [
            new TwigFilter('normalize', [StringNormalizer::class, 'normalize']),
        ];
    }
}
