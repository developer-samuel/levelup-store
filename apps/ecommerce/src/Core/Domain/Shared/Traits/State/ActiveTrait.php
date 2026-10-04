<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\State;

trait ActiveTrait
{
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
}
