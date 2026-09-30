<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Audit;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\RequestStack;

use App\Core\Domain\{
    Segment\Audit\Entity\AuditLog,
    Segment\Audit\Enum\AuditAction,
    Segment\User\Entity\User
};

use App\Core\Ports\Segment\Audit\AuditLoggerContract;

use App\Infrastructure\Shared\Http\RequestMetadata;

final readonly class AuditLogger implements AuditLoggerContract
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param RequestStack $requestStack
     * @param bool $enabled
    */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private bool $enabled,
    ) {}

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
