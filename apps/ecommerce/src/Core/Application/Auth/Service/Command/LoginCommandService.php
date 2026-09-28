<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Command;

use App\Core\Domain\{
    Auth\ValueObject\JwtTokenObject,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Auth\Service\Command\LoginCommandContract,
    Auth\Repository\RefreshTokenRepositoryContract,
    Gateways\External\Jwt\JwtGatewayContract
};

final readonly class LoginCommandService implements LoginCommandContract
{
    /**
     * @param JwtGatewayContract $jwtGateway
     * @param RefreshTokenRepositoryContract $refreshTokenRepository
    */
    public function __construct(
        private JwtGatewayContract $jwtGateway,
        private RefreshTokenRepositoryContract $refreshTokenRepository,
    ) {}

    /**
     * @param User $user
     *
     * @return JwtTokenObject
    */
    public function execute(User $user): JwtTokenObject
    {
        $accessToken = $this->jwtGateway->generateAccessToken($user);

        $refreshToken = $this->refreshTokenRepository->create($user);

        return new JwtTokenObject(
            accessToken:  $accessToken,
            refreshToken: $refreshToken->getToken(),
        );
    }
}
