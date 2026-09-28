<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Jwt;

use App\Core\Domain\Segment\User\Entity\User;

interface JwtGatewayContract
{
    /**
     * @param User $user
     *
     * @return string
    */
    public function generateAccessToken(User $user): string;
}
