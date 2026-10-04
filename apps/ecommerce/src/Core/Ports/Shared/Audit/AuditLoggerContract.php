<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Audit;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\User\Entity\User
};

interface AuditLoggerContract
{
    /** @param array<string, mixed> $metadata */
    public function log(
        AuditAction $action,
        string $entity,
        int $entityId,
        array $metadata = [],
        ?User $user = null,
    ): void;
}
