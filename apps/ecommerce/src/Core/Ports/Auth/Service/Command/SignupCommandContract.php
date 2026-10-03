<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Command;

use App\Core\Domain\{
    Auth\Payload\SignupPayload,
    Segment\User\Entity\User
};

interface SignupCommandContract
{
    public function signup(SignupPayload $payload): User;
}
