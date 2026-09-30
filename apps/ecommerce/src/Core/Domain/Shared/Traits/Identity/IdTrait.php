<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Identity;

/**
 * @property int $id
*/
trait IdTrait
{
    /**
     * @return int
    */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return self
    */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
}
