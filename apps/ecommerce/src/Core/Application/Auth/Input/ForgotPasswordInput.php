<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Input;

use App\Core\Application\{
    Segment\User\Constraint\ExistingEmail,
    Shared\Constraint\EmailFormat,
    Shared\Constraint\NotBlankConstraint
};

trait ForgotPasswordInput
{
    #[ExistingEmail]
    #[NotBlankConstraint('Email')]
    #[EmailFormat]
    public string $email;
}
