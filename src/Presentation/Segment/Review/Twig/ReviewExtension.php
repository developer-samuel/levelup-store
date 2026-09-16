<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Review\Twig;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};

use App\Core\Domain\Segment\Review\Traits\ReviewCoreTrait;

final class ReviewExtension extends AbstractExtension
{
    use ReviewCoreTrait;

    /**
     * @return TwigFunction[]
    */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('reviewValueText', static fn(float $value): string => self::resolveValueText($value)),
        ];
    }
}
