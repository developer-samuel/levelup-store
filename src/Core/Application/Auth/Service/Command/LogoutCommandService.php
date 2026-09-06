<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Command;

use App\Core\Ports\{
    Auth\Repository\RefreshTokenRepositoryContract,
    Auth\Service\Command\LogoutCommandContract,
    Gateways\Internal\Auth\TokenBlacklistContract
};

final readonly class LogoutCommandService implements LogoutCommandContract
{
    /**
     * @param RefreshTokenRepositoryContract $refreshTokenRepository
     * @param TokenBlacklistContract $tokenBlacklist
    */
    public function __construct(
        private RefreshTokenRepositoryContract $refreshTokenRepository,
        private TokenBlacklistContract $tokenBlacklist,
    ) {}

    /**
     * @param string|null $refreshToken
     *
     * @return void
    */
    public function execute(?string $refreshToken): void
    {
        if ($refreshToken === null || $refreshToken === '') {
            return;
        }

        $token = $this->refreshTokenRepository->findByToken($refreshToken);

        if ($token === null) {
            return;
        }

        $expiresAt = $token->getExpiresAt();

        $this->refreshTokenRepository->revoke($token);
        $this->tokenBlacklist->blacklist($refreshToken, $expiresAt);
    }
}
