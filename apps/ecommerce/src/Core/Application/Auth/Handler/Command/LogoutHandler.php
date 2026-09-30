<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Handler\Command;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Auth\Handler\Command\LogoutHandlerContract,
    Auth\Service\Command\LogoutCommandContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class LogoutHandler extends AbstractCommandHandler implements LogoutHandlerContract
{
    /**
     * @param LogoutCommandContract $logoutCommand
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly LogoutCommandContract $logoutCommand,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param string|null $refreshToken
     *
     * @return array<string, mixed>
    */
    public function handle(?string $refreshToken): array
    {
        return $this->execute(function () use ($refreshToken) {
            $this->logoutCommand->execute($refreshToken);

            return ApiResultFormatter::success('Logged out successfully');
        });
    }
}
