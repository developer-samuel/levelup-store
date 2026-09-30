<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Query;

use App\Core\Domain\Segment\User\Entity\User;

interface LoginRedirectQueryContract
{
    /**
     * @param User $user
     *
     * @return string
    */
    public function getRedirectRoute(User $user): string;
}
