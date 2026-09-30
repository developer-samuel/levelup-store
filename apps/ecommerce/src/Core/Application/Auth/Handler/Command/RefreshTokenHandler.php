<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Handler\Command;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Auth\Handler\Command\RefreshTokenHandlerContract,
    Auth\Service\Command\RefreshTokenCommandContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class RefreshTokenHandler extends AbstractCommandHandler implements RefreshTokenHandlerContract
{
    /**
     * @param RefreshTokenCommandContract $refreshTokenCommand
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly RefreshTokenCommandContract $refreshTokenCommand,
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
            if ($refreshToken === null || $refreshToken === '') {
                return ApiResultFormatter::error(401, 'Refresh token missing.');
            }

            $tokenPair = $this->refreshTokenCommand->execute($refreshToken);

            return ApiResultFormatter::success(
                'Token refreshed',
                ['access_token' => $tokenPair->accessToken],
            ) + ['refresh_token' => $tokenPair->refreshToken];
        });
    }
}
