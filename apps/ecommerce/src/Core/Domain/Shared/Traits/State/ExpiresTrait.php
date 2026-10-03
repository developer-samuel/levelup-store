<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\State;

use Doctrine\ORM\Mapping as ORM;

trait ExpiresTrait
{
    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    private \DateTimeImmutable $expiresAt;

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }
}
