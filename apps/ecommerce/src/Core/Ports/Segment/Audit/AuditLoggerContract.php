<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Audit;

use App\Core\Domain\{
    Segment\Audit\Enum\AuditAction,
    Segment\User\Entity\User
};

interface AuditLoggerContract
{
    /**
     * @param AuditAction $action
     * @param string $entity
     * @param int $entityId
     * @param array<string, mixed> $metadata
     * @param User|null $user
     *
     * @return void
    */
    public function log(
        AuditAction $action,
        string $entity,
        int $entityId,
        array $metadata = [],
        ?User $user = null,
    ): void;
}
