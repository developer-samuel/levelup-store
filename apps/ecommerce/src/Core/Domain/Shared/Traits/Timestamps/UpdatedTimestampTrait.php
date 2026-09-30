<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Timestamps;

use Doctrine\ORM\Mapping as ORM;

/**
 * @property \DateTimeImmutable|null $updatedAt
*/
trait UpdatedTimestampTrait
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @return \DateTimeImmutable
    */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return void
    */
    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
