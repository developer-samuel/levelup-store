<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\State;

use Doctrine\ORM\Mapping as ORM;

/**
 * @property \DateTimeImmutable $expiresAt
*/
trait ExpiresTrait
{
    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    private \DateTimeImmutable $expiresAt;

    /**
     * @return \DateTimeImmutable
    */
    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTimeImmutable $expiresAt
     *
     * @return self
    */
    public function setExpiresAt(\DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return bool
    */
    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }
}
