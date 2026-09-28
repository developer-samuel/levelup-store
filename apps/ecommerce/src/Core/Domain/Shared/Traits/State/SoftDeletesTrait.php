<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\State;

use Doctrine\ORM\Mapping as ORM;

/**
 * @property ?\DateTimeImmutable $deletedAt
*/
trait SoftDeletesTrait
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    /**
     * @return \DateTimeImmutable|null
    */
    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * @return void
    */
    public function setDeletedAt(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
    }

    /**
     * @return bool
    */
    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
