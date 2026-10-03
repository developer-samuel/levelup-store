<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Timestamps;

use Doctrine\ORM\Mapping as ORM;

trait UpdatedTimestampTrait
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
