<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Traits;

use App\Core\Domain\{
    Segment\User\Entity\UserBilling,
    Segment\User\Entity\UserShipping,
    Segment\User\Enum\UserRole
};

trait UserCoreTrait
{
    /** @return non-empty-string */
    public function getUserIdentifier(): string
    {
        $email = $this->getEmail();
        assert($email !== '');

        return $email;
    }

    public function eraseCredentials(): void {}

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /** @return string[] */
    public function getRoles(): array
    {
        return ['ROLE_' . strtoupper($this->role->value)];
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function setRole(UserRole $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getUseShipping(): bool
    {
        return $this->useShipping;
    }

    public function setUseShipping(bool $useShipping): void
    {
        $this->useShipping = $useShipping;
    }

    public function getEmailVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function setEmailVerifiedAt(\DateTimeImmutable $emailVerifiedAt): self
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
        return $this;
    }

    public function getBilling(): ?UserBilling
    {
        return $this->billing;
    }

    public function setBilling(?UserBilling $billing): self
    {
        $this->billing = $billing;
        if ($billing !== null && $billing->getUser() !== $this) {
            $billing->setUser($this);
        }

        return $this;
    }

    public function getShipping(): ?UserShipping
    {
        return $this->shipping;
    }

    public function setShipping(?UserShipping $shipping): self
    {
        $this->shipping = $shipping;
        if ($shipping !== null && $shipping->getUser() !== $this) {
            $shipping->setUser($this);
        }

        return $this;
    }
}
