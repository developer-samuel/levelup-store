<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Identity;

trait CodeTrait
{
    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }
}
