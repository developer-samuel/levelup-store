<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Handler\Command;

use App\Core\Domain\{
    Auth\Payload\SignupPayload,
    Segment\Audit\Enum\AuditAction
};

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Auth\Handler\Command\SignupHandlerContract,
    Auth\Service\Command\LoginCommandContract,
    Auth\Service\Command\SignupCommandContract,
    Auth\Service\Command\VerificationCommandContract,
    Auth\Service\Query\LoginRedirectQueryContract,
    Segment\Audit\AuditLoggerContract,
    Shared\Logging\AppLoggerContract,
    Shared\RateLimiter\RateLimiterContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class SignupHandler extends AbstractCommandHandler implements SignupHandlerContract
{
    /**
     * @param SignupCommandContract $signupCommand
     * @param VerificationCommandContract $verificationCommand
     * @param LoginCommandContract $loginCommand
     * @param LoginRedirectQueryContract $loginRedirectQuery
     * @param RateLimiterContract $rateLimiter
     * @param AuditLoggerContract $audit
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly SignupCommandContract $signupCommand,
        private readonly VerificationCommandContract $verificationCommand,
        private readonly LoginCommandContract $loginCommand,
        private readonly LoginRedirectQueryContract $loginRedirectQuery,
        private readonly RateLimiterContract $rateLimiter,
        private readonly AuditLoggerContract $audit,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param SignupPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(SignupPayload $payload): array
    {
        return $this->execute(function() use ($payload) {
            $this->rateLimiter->track();

            $user = $this->signupCommand->signup($payload);

            $this->verificationCommand->createAndSaveTokenForUser($user);

            $tokenPair = $this->loginCommand->execute($user);

            $this->rateLimiter->reset();

            $this->audit->log(AuditAction::SIGNUP, 'User', $user->getId(), [], $user);

            return ApiResultFormatter::success(
                'User registered and verification email sent.',
                [
                    'access_token' => $tokenPair->accessToken,
                    'redirect'     => $this->loginRedirectQuery->getRedirectRoute($user),
                ],
            ) + ['refresh_token' => $tokenPair->refreshToken];
        });
    }
}
