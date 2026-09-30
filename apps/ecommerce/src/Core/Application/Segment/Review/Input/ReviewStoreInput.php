<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Input;

use App\Core\Application\{
    Shared\Constraint\Length\AllMaxLengthConstraint,
    Shared\Constraint\Length\MaxLengthConstraint
};

trait ReviewStoreInput
{
    public string $variantId;

    public float $value;

    #[MaxLengthConstraint('Review body', 250)]
    public ?string $body;

    /** @var string[] */
    #[AllMaxLengthConstraint('Positive detail', 80)]
    public array $positives;

    /** @var string[] */
    #[AllMaxLengthConstraint('Negative detail', 80)]
    public array $negatives;
}
