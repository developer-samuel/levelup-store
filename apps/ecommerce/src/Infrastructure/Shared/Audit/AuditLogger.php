<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Audit;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\RequestStack;

use App\Core\Domain\{
    Shared\Audit\Entity\AuditLog,
    Shared\Audit\Enum\AuditAction,
    Segment\User\Entity\User
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

use App\Infrastructure\Shared\Http\RequestMetadata;

final readonly class AuditLogger implements AuditLoggerContract
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private bool $enabled,
    ) {}

    /** @param array<string, mixed> $metadata */
    public function log(
        AuditAction $action,
        string $entity,
        int $entityId,
        array $metadata = [],
        ?User $user = null,
    ): void {
        if (!$this->enabled) {
            return;
        }

        $meta = RequestMetadata::fromRequestStack($this->requestStack);

        $metadata['ip'] = $meta->ip;
        $metadata['user_agent'] = $meta->userAgent;

        $auditLog = new AuditLog($action, $entity, $entityId, $metadata, $user);

        $this->entityManager->persist($auditLog);
        $this->entityManager->flush();
    }
}
