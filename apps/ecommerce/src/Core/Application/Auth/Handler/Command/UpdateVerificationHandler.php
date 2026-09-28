<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Handler\Command;

use App\Core\Domain\{
    Auth\Payload\UpdateVerificationPayload,
    Auth\ValueObject\JwtTokenObject,
    Segment\Audit\Enum\AuditAction
};

use App\Core\Ports\{
    Auth\Handler\Command\UpdateVerificationHandlerContract,
    Auth\Service\Command\LoginCommandContract,
    Auth\Service\Command\VerificationCommandContract,
    Segment\Audit\AuditLoggerContract,
    Shared\Logging\AppLoggerContract
};

final readonly class UpdateVerificationHandler implements UpdateVerificationHandlerContract
{
    /**
     * @param VerificationCommandContract $verificationCommand
     * @param AuditLoggerContract $audit
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private VerificationCommandContract $verificationCommand,
        private LoginCommandContract $loginCommand,
        private AuditLoggerContract $audit,
        private AppLoggerContract $logger,
    ) {}

    /**
     * @param UpdateVerificationPayload $payload
     *
     * @return JwtTokenObject|null
    */
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

            $this->audit->log(AuditAction::EMAIL_VERIFIED, 'User', $user->getId(), [], $user);

            return $this->loginCommand->execute($user);
        } catch (\Exception $exception) {
            $this->logger->error('Verification update failed', $exception, null, [
                'token' => $payload->token,
            ]);

            throw $exception;
        }
    }
}
