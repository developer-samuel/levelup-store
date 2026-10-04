<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Handler\Command;

use App\Core\Domain\Segment\User\Payload\ChangePasswordPayload;

interface ChangePasswordCommandHandlerContract
{
    /** @return array<string, mixed> */
    public function handle(ChangePasswordPayload $payload): array;
}
