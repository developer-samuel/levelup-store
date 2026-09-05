<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Handler\Command;

use App\Core\Domain\{
    Segment\Audit\Enum\AuditAction,
    Segment\User\Payload\ProfilePayload
};

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Audit\AuditLoggerContract,
    Segment\User\Handler\Command\UpdateProfileHandlerContract,
    Segment\User\Service\Command\ProfileCommandContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class UpdateProfileHandler extends AbstractCommandHandler implements UpdateProfileHandlerContract
{
    /**
     * @param SecurityPolicyContract $securityPolicy
     * @param ProfileCommandContract $profileCommand
     * @param AuditLoggerContract $audit
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly SecurityPolicyContract $securityPolicy,
        private readonly ProfileCommandContract $profileCommand,
        private readonly AuditLoggerContract $audit,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param ProfilePayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(ProfilePayload $payload): array
    {
        return $this->execute(function() use ($payload) {
            $user = $this->securityPolicy->checkIfEmailVerified();

            $this->profileCommand->updateProfile($user, $payload);

            $this->audit->log(AuditAction::PROFILE_UPDATE, 'User', $user->getId(), [], $user);

            return ApiResultFormatter::success('Profile updated successfully.');
        });
    }
}
