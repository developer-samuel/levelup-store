<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\ValueObject\Email;

use App\Core\Domain\Segment\User\Entity\User;

/**
 * @phpstan-type ObjectArray array{
 *     verificationUrl: string,
 *     user: User
 * }
*/
final readonly class VerificationEmailObject
{
    /**
     * @param string $verificationUrl
     * @param User $user
    */
    public function __construct(
        public string $verificationUrl,
        public User $user,
    ) {}

    /**
     * @return ObjectArray
    */
    public function toArray(): array
    {
        return [
            'verificationUrl' => $this->verificationUrl,
            'user'            => $this->user,
        ];
    }
}
