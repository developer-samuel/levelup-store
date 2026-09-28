<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Command;

use App\Core\Domain\{
    Auth\ValueObject\JwtTokenObject,
    Segment\User\Entity\User
};

interface LoginCommandContract
{
    /**
     * @param User $user
     *
     * @return JwtTokenObject
    */
    public function execute(User $user): JwtTokenObject;
}
