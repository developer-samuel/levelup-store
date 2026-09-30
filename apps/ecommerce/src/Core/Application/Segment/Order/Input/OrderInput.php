<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Input;

use App\Core\Application\{
    Shared\Constraint\EmailFormat,
    Shared\Constraint\NotBlankConstraint,
    Shared\Input\NameFields
};

trait OrderInput
{
    use NameFields;

    #[NotBlankConstraint('Email')]
    #[EmailFormat]
    public string $email;

    public bool $send_shipping;
    public string $payment_method;
}
