<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Handler\Command;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\User\Entity\User,
    Segment\User\Payload\ChangePasswordPayload
};

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Shared\Audit\AuditLoggerContract,
    Segment\User\Handler\Command\ChangePasswordCommandHandlerContract,
    Segment\User\Service\Command\ChangePasswordCommandContract,
    Segment\User\Service\Query\ChangePasswordQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class ChangePasswordCommandHandler extends AbstractCommandHandler implements ChangePasswordCommandHandlerContract
{
    public function __construct(
        private readonly SecurityPolicyContract $securityPolicy,
        private readonly ChangePasswordQueryContract $changePasswordQuery,
        private readonly ChangePasswordCommandContract $changePasswordCommand,
        private readonly AuditLoggerContract $audit,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /** @return array<string, mixed> */
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

    private function validatePasswords(ChangePasswordPayload $payload, User $user): void
    {
        $this->changePasswordQuery->requireOldPassword($payload, $user);
        $this->changePasswordQuery->requireNewPassword($payload, $user);
    }
}
