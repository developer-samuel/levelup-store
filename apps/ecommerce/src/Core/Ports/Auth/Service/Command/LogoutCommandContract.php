<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Command;

interface LogoutCommandContract
{
    /**
     * @param string|null $refreshToken
     *
     * @return void
    */
    public function execute(?string $refreshToken): void;
}
