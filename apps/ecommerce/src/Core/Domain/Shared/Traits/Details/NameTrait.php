<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

/**
 * @property string $name
*/
trait NameTrait
{
    /**
     * @return string
    */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
    */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
