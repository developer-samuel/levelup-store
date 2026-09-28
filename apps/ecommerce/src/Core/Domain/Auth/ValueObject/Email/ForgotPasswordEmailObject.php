<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\ValueObject\Email;

use App\Core\Domain\Segment\User\Entity\User;

/**
 * @phpstan-type ObjectArray array{
 *     resetUrl: string,
 *     user: User
 * }
*/
final readonly class ForgotPasswordEmailObject
{
    /**
     * @param string $resetUrl
     * @param User $user
     */
    public function __construct(
        public string $resetUrl,
        public User $user,
    ) {}

    /**
     * @return ObjectArray
    */
    public function toArray(): array
    {
        return [
            'resetUrl' => $this->resetUrl,
            'user'     => $this->user,
        ];
    }
}
