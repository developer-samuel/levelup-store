<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Handler\Command;

use App\Core\Domain\Auth\Payload\ResetPasswordPayload;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Auth\Handler\Command\ResetPasswordCommandHandlerContract,
    Auth\Service\Command\ResetPasswordCommandContract,
    Auth\Service\Query\ResetPasswordQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

class ResetPasswordCommandHandler extends AbstractCommandHandler implements ResetPasswordCommandHandlerContract
{
    /**
     * @param ResetPasswordQueryContract $resetPasswordQuery
     * @param ResetPasswordCommandContract $resetPasswordCommand
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ResetPasswordQueryContract $resetPasswordQuery,
        private readonly ResetPasswordCommandContract $resetPasswordCommand,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param ResetPasswordPayload $payload
     *
     * @return array<string, mixed>
     */
    public function handle(ResetPasswordPayload $payload): array
    {
        return $this->execute(function() use ($payload) {
            $user = $this->resetPasswordQuery->getValidUserWithToken($payload->token);

            $this->resetPasswordCommand->resetPassword($user, $payload->password);

            return ApiResultFormatter::success('Password has been successfully reset.', [
                'redirect' => '/login',
            ]);
        });
    }
}
