<?php

declare(strict_types=1);

namespace App\Shared\Traits\Identity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @property string $token
*/
trait TokenTrait
{
    #[ORM\Column(type: 'string', length: 128, unique: true, nullable: false)]
    private string $token;

    /**
     * @return string
    */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * 
     * @return self
    */
    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }
}
