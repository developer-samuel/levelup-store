<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

trait NameTrait
{
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
