<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Traits;

use App\Core\Domain\{
    Segment\User\Entity\UserBilling,
    Segment\User\Entity\UserShipping,
    Segment\User\Enum\UserRole
};

/**
 * @property string $email
 * @property string $password
 * @property UserRole $role
 * @property bool $useShipping
 * @property \DateTimeImmutable|null $emailVerifiedAt
 * @property UserBilling|null $billing
 * @property UserShipping|null $shipping
*/
trait UserCoreTrait
{
    /**
     * @return non-empty-string
    */
    public function getUserIdentifier(): string
    {
        $email = $this->getEmail();
        assert($email !== '');

        return $email;
    }

    /**
     * @return void
    */
    public function eraseCredentials(): void {}

    /**
     * @return string
    */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return self
    */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string[]
    */
    public function getRoles(): array
    {
        return ['ROLE_' . strtoupper($this->role->value)];
    }

    /**
     * @return UserRole
    */
    public function getRole(): UserRole
    {
        return $this->role;
    }

    /**
     * @param UserRole $role
     *
     * @return self
    */
    public function setRole(UserRole $role): self
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return bool
    */
    public function getUseShipping(): bool
    {
        return $this->useShipping;
    }

    /**
     * @param bool $useShipping
     *
     * @return void
    */
    public function setUseShipping(bool $useShipping): void
    {
        $this->useShipping = $useShipping;
    }

    /**
     * @return \DateTimeImmutable|null
    */
    public function getEmailVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    /**
     * @param \DateTimeImmutable $emailVerifiedAt
     *
     * @return self
    */
    public function setEmailVerifiedAt(\DateTimeImmutable $emailVerifiedAt): self
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
        return $this;
    }

    /**
     * @return UserBilling|null
    */
    public function getBilling(): ?UserBilling
    {
        return $this->billing;
    }

    /**
     * @param UserBilling|null $billing
     *
     * @return self
    */
    public function setBilling(?UserBilling $billing): self
    {
        $this->billing = $billing;
        if ($billing !== null && $billing->getUser() !== $this) {
            $billing->setUser($this);
        }

        return $this;
    }

    /**
     * @return UserShipping|null
    */
    public function getShipping(): ?UserShipping
    {
        return $this->shipping;
    }

    /**
     * @param UserShipping|null $shipping
     *
     * @return self
    */
    public function setShipping(?UserShipping $shipping): self
    {
        $this->shipping = $shipping;
        if ($shipping !== null && $shipping->getUser() !== $this) {
            $shipping->setUser($this);
        }

        return $this;
    }
}
