<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Handler\Command;

use App\Core\Domain\{
    Auth\Payload\UpdateVerificationPayload,
    Auth\ValueObject\JwtTokenObject
};

use App\Core\Ports\{
    Auth\Handler\Command\UpdateVerificationHandlerContract,
    Auth\Notifier\UserNotifierContract,
    Auth\Service\Command\LoginCommandContract,
    Auth\Service\Command\VerificationCommandContract,
    Shared\Logging\AppLoggerContract
};

final readonly class UpdateVerificationHandler implements UpdateVerificationHandlerContract
{
    public function __construct(
        private VerificationCommandContract $verificationCommand,
        private LoginCommandContract $loginCommand,
        private UserNotifierContract $userNotifier,
        private AppLoggerContract $logger,
    ) {}

    public function handle(UpdateVerificationPayload $payload): ?JwtTokenObject
    {
        try {
            if ($payload->token === '') {
                return null;
            }

            $user = $this->verificationCommand->verifyUserByToken($payload);

            if ($user === null) {
                return null;
            }

            $this->userNotifier->notifyEmailVerified($user);

            return $this->loginCommand->execute($user);
        } catch (\Exception $exception) {
            $this->logger->error('Verification update failed', $exception, null, [
                'token' => $payload->token,
            ]);

            throw $exception;
        }
    }
}
