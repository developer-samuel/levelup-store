<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Traits;

trait UserEmailTrait
{
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
}
