<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\State;

trait PositionTrait
{
    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }
}
