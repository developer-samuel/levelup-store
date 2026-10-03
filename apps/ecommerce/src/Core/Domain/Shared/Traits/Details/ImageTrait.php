<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

trait ImageTrait
{
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }
}
