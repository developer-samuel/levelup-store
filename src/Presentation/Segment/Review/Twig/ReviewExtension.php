<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Review\Twig;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};

use App\Core\Domain\Segment\Review\Traits\ReviewCoreTrait;

class ReviewExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
    */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('reviewValueText', static fn(float $value): string => ReviewCoreTrait::resolveValueText($value)),
        ];
    }
}
