<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Handler\Command;

use App\Core\Domain\Auth\Payload\LoginPayload;

interface LoginHandlerContract
{
    /**
     * @param LoginPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(LoginPayload $payload): array;
}
