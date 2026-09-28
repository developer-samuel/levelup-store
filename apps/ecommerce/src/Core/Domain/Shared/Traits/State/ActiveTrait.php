<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\State;

/**
 * @property bool $isActive
*/
trait ActiveTrait
{
    /**
     * @return bool
    */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return self
    */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
}
