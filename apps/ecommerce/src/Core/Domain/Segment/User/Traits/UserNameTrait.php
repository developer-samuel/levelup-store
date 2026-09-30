<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Traits;

/**
 * @property string $firstName
 * @property string $lastName
*/
trait UserNameTrait
{
    /**
     * @return string
    */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * 
     * @return self
    */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
    */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * 
     * @return self
    */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
    */
    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }
}
