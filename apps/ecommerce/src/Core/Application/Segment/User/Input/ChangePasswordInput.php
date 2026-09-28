<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Input;

use App\Core\Domain\Segment\Password\PasswordConstants;

use App\Core\Application\{
    Shared\Constraint\Length\MaxLengthConstraint,
    Shared\Constraint\Length\MinLengthConstraint,
    Shared\Constraint\NumberConstraint,
    Shared\Constraint\SpecialCharacterConstraint,
    Shared\Constraint\UppercaseConstraint,
    Shared\Constraint\NotBlankConstraint
};

trait ChangePasswordInput
{
    #[NotBlankConstraint(PasswordConstants::OLD_PASSWORD)]
    #[MinLengthConstraint(PasswordConstants::OLD_PASSWORD, 8)]
    #[MaxLengthConstraint(PasswordConstants::OLD_PASSWORD, 100)]
    #[UppercaseConstraint(PasswordConstants::OLD_PASSWORD)]
    #[NumberConstraint(PasswordConstants::OLD_PASSWORD)]
    #[SpecialCharacterConstraint(PasswordConstants::OLD_PASSWORD)]
    public string $old_password;

    #[NotBlankConstraint(PasswordConstants::NEW_PASSWORD)]
    #[MinLengthConstraint(PasswordConstants::NEW_PASSWORD, 8)]
    #[MaxLengthConstraint(PasswordConstants::NEW_PASSWORD, 100)]
    #[UppercaseConstraint(PasswordConstants::NEW_PASSWORD)]
    #[NumberConstraint(PasswordConstants::NEW_PASSWORD)]
    #[SpecialCharacterConstraint(PasswordConstants::NEW_PASSWORD)]
    public string $new_password;

    #[NotBlankConstraint(PasswordConstants::NEW_PASSWORD_CONFIRMATION)]
    public string $new_password_confirmation;
}
