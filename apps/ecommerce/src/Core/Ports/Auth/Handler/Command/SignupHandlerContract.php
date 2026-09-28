<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Handler\Command;

use App\Core\Domain\Auth\Payload\SignupPayload;

interface SignupHandlerContract
{
    /**
     * @param SignupPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(SignupPayload $payload): array;
}
