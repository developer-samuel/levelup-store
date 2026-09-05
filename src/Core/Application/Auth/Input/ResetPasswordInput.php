<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Input;

use App\Core\Domain\Segment\Password\PasswordConstants;

use App\Core\Application\{
    Shared\Constraint\Length\MaxLengthConstraint,
    Shared\Constraint\Length\MinLengthConstraint,
    Shared\Constraint\NumberConstraint,
    Shared\Constraint\SpecialCharacterConstraint,
    Shared\Constraint\UppercaseConstraint,
    Shared\Constraint\NotBlankConstraint
};

trait ResetPasswordInput
{
    #[NotBlankConstraint(PasswordConstants::PASSWORD)]
    #[MinLengthConstraint(PasswordConstants::PASSWORD, 8)]
    #[MaxLengthConstraint(PasswordConstants::PASSWORD, 100)]
    #[UppercaseConstraint(PasswordConstants::PASSWORD)]
    #[NumberConstraint(PasswordConstants::PASSWORD)]
    #[SpecialCharacterConstraint(PasswordConstants::PASSWORD)]
    public string $password;

    #[NotBlankConstraint(PasswordConstants::PASSWORD_CONFIRMATION)]
    public string $password_confirmation;
}
