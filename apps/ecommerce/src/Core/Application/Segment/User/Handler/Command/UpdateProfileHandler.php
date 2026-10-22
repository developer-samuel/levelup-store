<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Handler\Command;

use App\Core\Domain\Segment\User\Payload\ProfilePayload;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\User\Handler\Command\UpdateProfileHandlerContract,
    Segment\User\Notifier\UserNotifierContract,
    Segment\User\Service\Command\ProfileCommandContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class UpdateProfileHandler extends AbstractCommandHandler implements UpdateProfileHandlerContract
{
    public function __construct(
        private readonly SecurityPolicyContract $securityPolicy,
        private readonly ProfileCommandContract $profileCommand,
        private readonly UserNotifierContract $userNotifier,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /** @return array<string, mixed> */
    public function handle(ProfilePayload $payload): array
    {
        return $this->execute(function () use ($payload) {
            $user = $this->securityPolicy->checkIfEmailVerified();

            $this->profileCommand->updateProfile($user, $payload);

            $this->userNotifier->notifyProfileUpdated($user);

            return ApiResultFormatter::success('Profile updated successfully.');
        });
    }
}
