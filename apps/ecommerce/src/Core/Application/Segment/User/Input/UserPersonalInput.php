<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Input;

use App\Core\Application\Shared\Input\NameFields;

trait UserPersonalInput
{
    use NameFields;

    public bool $use_shipping;
}
