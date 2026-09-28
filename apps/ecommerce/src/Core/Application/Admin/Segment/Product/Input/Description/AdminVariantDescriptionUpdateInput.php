<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Input\Description;

use App\Core\Application\{
    Shared\Constraint\Length\MaxLengthConstraint,
    Shared\Constraint\Length\MinLengthConstraint,
    Shared\Constraint\NotBlankConstraint,
    Shared\Input\DecryptedId
};

trait AdminVariantDescriptionUpdateInput
{
    use DecryptedId;

    #[NotBlankConstraint('ID')]
    public string $id;

    #[NotBlankConstraint('Variant ID')]
    public string $variantId;

    #[MinLengthConstraint('Position', 1)]
    public int $position;

    #[MinLengthConstraint('Title', 5)]
    #[MaxLengthConstraint('Title', 255)]
    public string $title;

    #[NotBlankConstraint('Body')]
    public string $body;
}
