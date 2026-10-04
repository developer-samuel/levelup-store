<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Identity;

trait IdTrait
{
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
}
