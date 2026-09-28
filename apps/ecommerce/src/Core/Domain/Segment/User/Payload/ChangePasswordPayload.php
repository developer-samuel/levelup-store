<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Payload;

final readonly class ChangePasswordPayload
{
    /**
     * @param string $oldPassword
     * @param string $newPassword
     * @param string $newPasswordConfirmation
    */
    public function __construct(
        public string $oldPassword,
        #[\SensitiveParameter] public string $newPassword,
        #[\SensitiveParameter] public string $newPasswordConfirmation,
    ) {}
}
