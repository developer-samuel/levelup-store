<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Handler\Command;

use App\Core\Domain\Auth\Payload\ForgotPasswordPayload;

interface ForgotPasswordCommandHandlerContract
{
    /**
     * @param ForgotPasswordPayload $payload
     *
     * @return array<string, mixed>
     */
    public function handle(ForgotPasswordPayload $payload): array;
}
