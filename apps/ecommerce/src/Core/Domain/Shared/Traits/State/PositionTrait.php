<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\State;

/**
 * @property int $position
*/
trait PositionTrait
{
    /**
     * @return int
    */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return self
    */
    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }
}
