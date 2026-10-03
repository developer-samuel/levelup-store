<?php

declare(strict_types=1);

namespace App\Shared\Traits\Identity;

use Doctrine\ORM\Mapping as ORM;

trait TokenTrait
{
    #[ORM\Column(type: 'string', length: 128, unique: true, nullable: false)]
    private string $token;

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }
}
