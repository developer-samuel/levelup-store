<?php

declare(strict_types=1);

namespace App\Adapters\External\Jwt;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Gateways\External\Jwt\JwtGatewayContract;

final readonly class JwtAdapter implements JwtGatewayContract
{
    /**
     * @param JWTTokenManagerInterface $jwtManager
    */
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
    ) {}

    /**
     * @param User $user
     *
     * @return string
    */
    public function generateAccessToken(User $user): string
    {
        return $this->jwtManager->create($user);
    }
}
