<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Audit\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Audit\Enum\AuditAction,
    Segment\User\Entity\User,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'audit_logs')]
#[ORM\HasLifecycleCallbacks]
class AuditLog
{
    use IdTrait;
    use CreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user;

    #[ORM\Column(type: 'string', length: 64)]
    private string $action;

    #[ORM\Column(type: 'string', length: 64)]
    private string $entity;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $entityId;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $metadata;

    /**
     * @param AuditAction $action
     * @param string $entity
     * @param int $entityId
     * @param array<string, mixed> $metadata
     * @param User|null $user
    */
    public function __construct(
        AuditAction $action,
        string $entity,
        int $entityId,
        array $metadata = [],
        ?User $user = null,
    ) {
        $this->action = $action->value;
        $this->entity = $entity;
        $this->entityId = $entityId;
        $this->metadata = $metadata !== [] ? json_encode($metadata, JSON_THROW_ON_ERROR) : null;
        $this->user = $user;
    }

    /**
     * @return User|null
    */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return AuditAction
    */
    public function getAction(): AuditAction
    {
        return AuditAction::from($this->action);
    }

    /**
     * @return string
    */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return int
    */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @return array<string, mixed>
    */
    public function getMetadata(): array
    {
        if ($this->metadata === null) {
            return [];
        }

        /** @var array<string, mixed> */
        return json_decode($this->metadata, true, 512, JSON_THROW_ON_ERROR);
    }
}
