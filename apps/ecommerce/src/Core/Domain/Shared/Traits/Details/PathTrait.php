<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

/**
 * @property string $path
*/
trait PathTrait
{
    /**
     * @return string
    */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return self
    */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }
}
