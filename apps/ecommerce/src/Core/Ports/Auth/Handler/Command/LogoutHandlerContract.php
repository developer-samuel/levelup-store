<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Handler\Command;

interface LogoutHandlerContract
{
    /**
     * @param string|null $refreshToken
     *
     * @return array<string, mixed>
    */
    public function handle(?string $refreshToken): array;
}
