<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

trait PathTrait
{
    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }
}
