<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Timestamps;

use Doctrine\ORM\Mapping as ORM;

/**
 * @property \DateTimeImmutable $createdAt
*/
trait CreatedTimestampTrait
{
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * @return \DateTimeImmutable
    */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return void
    */
    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
