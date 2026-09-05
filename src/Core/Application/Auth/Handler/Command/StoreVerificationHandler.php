<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Handler\Command;

use App\Core\Domain\{
    Shared\Exception\ConflictException,
    Segment\User\Entity\User
};

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Auth\Handler\Command\StoreVerificationHandlerContract,
    Auth\Service\Command\VerificationCommandContract,
    Security\SecurityPolicyContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

class StoreVerificationHandler extends AbstractCommandHandler implements StoreVerificationHandlerContract
{
    /**
     * @param SecurityPolicyContract $securityPolicy
     * @param VerificationCommandContract $verificationCommand
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly SecurityPolicyContract $securityPolicy,
        private readonly VerificationCommandContract $verificationCommand,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @return array<string, mixed>
    */
    public function handle(): array
    {
        return $this->execute(function() {
            $user = $this->getValidUser();

            $this->verificationCommand->createAndSaveTokenForUser($user);

            return ApiResultFormatter::success(
                'We have sent you a verification link to "' . $user->getEmail() . '".',
            );
        });
    }

    /**
     * @return User
     *
     * @throws ConflictException
    */
    private function getValidUser(): User
    {
        $user = $this->securityPolicy->checkAccess();

        if ($user->getEmailVerifiedAt() !== null) {
            throw new ConflictException('User email already verified.');
        }

        return $user;
    }
}
