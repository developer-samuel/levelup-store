<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Command;

use App\Core\Domain\Auth\ValueObject\JwtTokenObject;

interface RefreshTokenCommandContract
{
    /**
     * @param string $refreshToken
     *
     * @return JwtTokenObject
    */
    public function execute(string $refreshToken): JwtTokenObject;
}
