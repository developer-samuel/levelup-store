<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Handler\Command;

use App\Core\Domain\{
    Auth\Payload\LoginPayload,
    Segment\Audit\Enum\AuditAction,
    Segment\User\Entity\User
};

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Auth\Handler\Command\LoginHandlerContract,
    Auth\Service\Command\LoginCommandContract,
    Auth\Service\Query\LoginRedirectQueryContract,
    Security\Provider\PasswordHasherProviderContract,
    Segment\Audit\AuditLoggerContract,
    Segment\User\Repository\UserRepositoryContract,
    Shared\Logging\AppLoggerContract,
    Shared\RateLimiter\RateLimiterContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class LoginHandler extends AbstractCommandHandler implements LoginHandlerContract
{
    /**
     * @param UserRepositoryContract $userRepository
     * @param PasswordHasherProviderContract $passwordHasherProvider
     * @param LoginCommandContract $loginCommand
     * @param LoginRedirectQueryContract $loginRedirectQuery
     * @param RateLimiterContract $rateLimiter
     * @param AuditLoggerContract $audit
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly UserRepositoryContract $userRepository,
        private readonly PasswordHasherProviderContract $passwordHasherProvider,
        private readonly LoginCommandContract $loginCommand,
        private readonly LoginRedirectQueryContract $loginRedirectQuery,
        private readonly RateLimiterContract $rateLimiter,
        private readonly AuditLoggerContract $audit,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param LoginPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(LoginPayload $payload): array
    {
        return $this->execute(function () use ($payload) {
            $this->rateLimiter->track();

            $user = $this->validateCredentials($payload);

            $tokenPair = $this->loginCommand->execute($user);

            $this->rateLimiter->reset();

            $this->audit->log(AuditAction::LOGIN, 'User', $user->getId(), [], $user);

            return ApiResultFormatter::success(
                'Login successful',
                [
                    'access_token' => $tokenPair->accessToken,
                    'redirect'     => $this->loginRedirectQuery->getRedirectRoute($user),
                ],
            ) + ['refresh_token' => $tokenPair->refreshToken];
        });
    }

    /**
     * @param LoginPayload $payload
     *
     * @return User
     *
     * @throws \DomainException
    */
    private function validateCredentials(LoginPayload $payload): User
    {
        $user = $this->userRepository->findByEmail($payload->email);

        if ($user === null || $user->isDeleted() || !$this->passwordHasherProvider->isPasswordValid($user, $payload->password)) {
            throw new \DomainException('Invalid credentials.');
        }

        return $user;
    }
}
