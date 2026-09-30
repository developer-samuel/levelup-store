<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Brand\Input;

use App\Core\Application\{
    Segment\Brand\Constraint\UniqueBrandName,
    Shared\Constraint\Length\MaxLengthConstraint,
    Shared\Constraint\NotBlankConstraint
};

trait AdminBrandStoreInput
{
    #[UniqueBrandName]
    #[NotBlankConstraint('Name')]
    #[MaxLengthConstraint('Name', 50)]
    public string $name;
}
