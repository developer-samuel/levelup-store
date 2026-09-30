<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Traits;

/**
 * @property string $email
*/
trait UserEmailTrait
{
    /**
     * @return string
    */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * 
     * @return self
    */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
}
