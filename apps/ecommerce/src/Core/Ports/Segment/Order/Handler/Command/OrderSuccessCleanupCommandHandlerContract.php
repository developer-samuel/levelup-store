<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Handler\Command;

use App\Core\Domain\Segment\User\Entity\User;

interface OrderSuccessCleanupCommandHandlerContract
{
    /**
     * @param string|null $sessionId
     * @param User $user
     *
     * @return array<string, mixed>
    */
    public function handle(?string $sessionId, User $user): array;
}
