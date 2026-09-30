<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Review\Twig;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};

final class ReviewExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
    */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('reviewValueText', static fn(float $value): string => self::resolveValueText($value)),
        ];
    }

    /**
     * @param float $value
     * 
     * @return string
    */
    private static function resolveValueText(float $value): string
    {
        return match (true) {
            $value >= 4.5 => 'Excellent',
            $value >= 3.5 => 'Very Good',
            $value >= 2.5 => 'Good',
            $value >= 1.5 => 'Fair',
            $value > 0    => 'Poor',
            default       => 'No rating',
        };
    }
}
