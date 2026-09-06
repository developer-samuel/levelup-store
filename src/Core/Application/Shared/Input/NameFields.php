<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Input;

use App\Core\Application\{
    Shared\Constraint\Length\MinLengthConstraint,
    Shared\Constraint\Length\MaxLengthConstraint,
    Shared\Constraint\NotBlankConstraint
};

trait NameFields
{
    private const FIRST_NAME = 'First name';
    private const LAST_NAME = 'Last name';

    #[NotBlankConstraint(self::FIRST_NAME)]
    #[MinLengthConstraint(self::FIRST_NAME, 2)]
    #[MaxLengthConstraint(self::FIRST_NAME, 100)]
    public string $first_name;

    #[NotBlankConstraint(self::LAST_NAME)]
    #[MinLengthConstraint(self::LAST_NAME, 2)]
    #[MaxLengthConstraint(self::LAST_NAME, 100)]
    public string $last_name;
}
