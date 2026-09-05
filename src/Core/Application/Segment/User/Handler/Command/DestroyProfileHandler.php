<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Handler\Command;

use App\Core\Domain\Segment\Audit\Enum\AuditAction;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Audit\AuditLoggerContract,
    Segment\User\Handler\Command\DestroyProfileHandlerContract,
    Segment\User\Service\Command\DestroyProfileCommandContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

class DestroyProfileHandler extends AbstractCommandHandler implements DestroyProfileHandlerContract
{
    /**
     * @param SecurityPolicyContract $securityPolicy
     * @param DestroyProfileCommandContract $destroyProfileCommand
     * @param AuditLoggerContract $audit
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly SecurityPolicyContract $securityPolicy,
        private readonly DestroyProfileCommandContract $destroyProfileCommand,
        private readonly AuditLoggerContract $audit,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @return array<string, mixed>
    */
    public function handle(): array
    {
        return $this->execute(function () {
            $user = $this->securityPolicy->checkIfEmailVerified();

            $this->destroyProfileCommand->destroyProfile($user);

            $this->audit->log(AuditAction::PROFILE_DESTROY, 'User', $user->getId(), [], $user);

            return ApiResultFormatter::success('Your account has been successfully deleted.');
        });
    }
}
