<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Command;

interface LogoutCommandContract
{
    public function execute(?string $refreshToken): void;
}
