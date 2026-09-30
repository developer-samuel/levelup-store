<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

/**
 * @property string|null $image
*/
trait ImageTrait
{
    /**
     * @return string|null
    */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     *
     * @return self
    */
    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }
}
