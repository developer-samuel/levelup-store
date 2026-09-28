<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Handler\Command;

use App\Core\Domain\Segment\User\Payload\ProfilePayload;

interface UpdateProfileHandlerContract
{
    /**
     * @param ProfilePayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(ProfilePayload $payload): array;
}
