<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Input;

use App\Core\Application\{
    Shared\Constraint\EmailFormat,
    Shared\Constraint\NotBlankConstraint
};

trait LoginInput
{
    #[NotBlankConstraint('Email')]
    #[EmailFormat]
    public string $email;

    #[NotBlankConstraint('Password')]
    public string $password;
}
