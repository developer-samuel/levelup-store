<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Renderer;

use App\Core\Domain\Segment\User\Entity\User;

interface ForgotPasswordEmailRendererContract
{
    /**
     * @param string $resetUrl
     * @param User $user
     *
     * @return string
    */
    public function renderForgotPasswordEmail(string $resetUrl, User $user): string;
}
