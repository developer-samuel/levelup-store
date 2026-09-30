<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Identity;

/**
 * @property string $code
*/
trait CodeTrait
{
    /**
     * @return string
    */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return self
    */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }
}
