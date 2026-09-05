<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Handler\Command;

use App\Core\Domain\{
    Segment\Audit\Enum\AuditAction,
    Segment\User\Entity\User,
    Segment\User\Payload\ChangePasswordPayload
};

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Audit\AuditLoggerContract,
    Segment\User\Handler\Command\ChangePasswordCommandHandlerContract,
    Segment\User\Service\Command\ChangePasswordCommandContract,
    Segment\User\Service\Query\ChangePasswordQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class ChangePasswordCommandHandler extends AbstractCommandHandler implements ChangePasswordCommandHandlerContract
{
    /**
     * @param SecurityPolicyContract $securityPolicy
     * @param ChangePasswordQueryContract $changePasswordQuery
     * @param ChangePasswordCommandContract $changePasswordCommand
     * @param AuditLoggerContract $audit
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly SecurityPolicyContract $securityPolicy,
        private readonly ChangePasswordQueryContract $changePasswordQuery,
        private readonly ChangePasswordCommandContract $changePasswordCommand,
        private readonly AuditLoggerContract $audit,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param ChangePasswordPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(ChangePasswordPayload $payload): array
    {
        return $this->execute(function() use ($payload) {
            $user = $this->securityPolicy->checkIfEmailVerified();

            $this->validatePasswords($payload, $user);

            $this->changePasswordCommand->changeUserPassword($user, $payload->newPassword);

            $this->audit->log(AuditAction::PASSWORD_CHANGE, 'User', $user->getId(), [], $user);

            return ApiResultFormatter::success('Password successfully changed.');
        });
    }

    /**
     * @param ChangePasswordPayload $payload
     * @param User $user
     *
     * @return void
    */
    private function validatePasswords(ChangePasswordPayload $payload, User $user): void
    {
        $this->changePasswordQuery->requireOldPassword($payload, $user);
        $this->changePasswordQuery->requireNewPassword($payload, $user);
    }
}
