<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Command;

use App\Core\Domain\Auth\ValueObject\JwtTokenObject;

use App\Core\Ports\{
    Auth\Service\Command\RefreshTokenCommandContract,
    Auth\Repository\RefreshTokenRepositoryContract,
    Gateways\External\Jwt\JwtGatewayContract,
    Gateways\Internal\Auth\TokenBlacklistContract
};

final readonly class RefreshTokenCommandService implements RefreshTokenCommandContract
{
    /**
     * @param JwtGatewayContract $jwtGateway
     * @param RefreshTokenRepositoryContract $refreshTokenRepository
     * @param TokenBlacklistContract $tokenBlacklist
    */
    public function __construct(
        private JwtGatewayContract $jwtGateway,
        private RefreshTokenRepositoryContract $refreshTokenRepository,
        private TokenBlacklistContract $tokenBlacklist,
    ) {}

    /**
     * @param string $refreshToken
     *
     * @return JwtTokenObject
     *
     * @throws \DomainException
    */
    public function execute(string $refreshToken): JwtTokenObject
    {
        if ($this->tokenBlacklist->isBlacklisted($refreshToken)) {
            throw new \DomainException('Token reuse detected. Please log in again.');
        }

        $token = $this->refreshTokenRepository->findByToken($refreshToken);

        if ($token === null || $token->isExpired()) {
            throw new \DomainException('Invalid or expired refresh token.');
        }

        $user      = $token->getUser();
        $expiresAt = $token->getExpiresAt();

        $this->refreshTokenRepository->revoke($token);
        $this->tokenBlacklist->blacklist($refreshToken, $expiresAt);

        $newAccessToken  = $this->jwtGateway->generateAccessToken($user);
        $newRefreshToken = $this->refreshTokenRepository->create($user);

        return new JwtTokenObject(
            accessToken:  $newAccessToken,
            refreshToken: $newRefreshToken->getToken(),
        );
    }
}
