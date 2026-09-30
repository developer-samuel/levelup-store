<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Handler\Command;

use App\Core\Domain\Auth\Payload\ForgotPasswordPayload;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Auth\Handler\Command\ForgotPasswordCommandHandlerContract,
    Auth\Service\Command\ForgotPasswordCommandContract,
    Segment\User\Service\Query\UserQueryContract,
    Shared\Logging\AppLoggerContract,
    Shared\RateLimiter\RateLimiterContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class ForgotPasswordCommandHandler extends AbstractCommandHandler implements ForgotPasswordCommandHandlerContract
{
    /**
     * @param ForgotPasswordCommandContract $forgotPasswordCommand
     * @param UserQueryContract $userQuery
     * @param RateLimiterContract $rateLimiter
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ForgotPasswordCommandContract $forgotPasswordCommand,
        private readonly UserQueryContract $userQuery,
        private readonly RateLimiterContract $rateLimiter,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param ForgotPasswordPayload $payload
     *
     * @return array<string, mixed>
     */
    public function handle(ForgotPasswordPayload $payload): array
    {
        return $this->execute(function() use ($payload) {
            $this->rateLimiter->track();

            $user = $this->userQuery->findUserByEmailOrFail($payload->email);

            $this->forgotPasswordCommand->createAndSaveTokenForUser($user);

            $this->rateLimiter->reset();

            return ApiResultFormatter::success('We have sent you an email with a link to change your password.');
        });
    }
}
