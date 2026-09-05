<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Input;

use App\Core\Domain\Segment\Password\PasswordConstants;

use App\Core\Application\{
    Segment\User\Constraint\UniqueEmail,
    Shared\Constraint\EmailFormat,
    Shared\Constraint\Length\MaxLengthConstraint,
    Shared\Constraint\Length\MinLengthConstraint,
    Shared\Constraint\NumberConstraint,
    Shared\Constraint\NotBlankConstraint,
    Shared\Constraint\SpecialCharacterConstraint,
    Shared\Constraint\UppercaseConstraint,
    Shared\Constraint\TermsAcceptedConstraint,
    Shared\Input\NameFields
};

trait SignupInput
{
    use NameFields;

    #[EmailFormat]
    #[UniqueEmail]
    #[NotBlankConstraint('Email')]
    #[MinLengthConstraint('Email', 5)]
    #[MaxLengthConstraint('Email', 255)]
    public ?string $email = null;

    #[NotBlankConstraint(PasswordConstants::PASSWORD)]
    #[MinLengthConstraint(PasswordConstants::PASSWORD, 8)]
    #[MaxLengthConstraint(PasswordConstants::PASSWORD, 100)]
    #[UppercaseConstraint(PasswordConstants::PASSWORD)]
    #[NumberConstraint(PasswordConstants::PASSWORD)]
    #[SpecialCharacterConstraint(PasswordConstants::PASSWORD)]
    public ?string $password = null;

    #[NotBlankConstraint(PasswordConstants::PASSWORD_CONFIRMATION)]
    public ?string $password_confirmation = null;

    #[TermsAcceptedConstraint]
    public bool $terms_and_conditions = false;
}
