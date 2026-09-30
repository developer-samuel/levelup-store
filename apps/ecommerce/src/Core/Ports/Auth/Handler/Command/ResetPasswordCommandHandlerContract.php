<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Handler\Command;

use App\Core\Domain\Auth\Payload\ResetPasswordPayload;

interface ResetPasswordCommandHandlerContract
{
    /**
     * @param ResetPasswordPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(ResetPasswordPayload $payload): array;
}
