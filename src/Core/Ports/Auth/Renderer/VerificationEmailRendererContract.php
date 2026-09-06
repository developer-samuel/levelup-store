<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Renderer;

use App\Core\Domain\Segment\User\Entity\User;

interface VerificationEmailRendererContract
{
    /**
     * @param string $verificationUrl
     * @param User $user
     *
     * @return string
    */
    public function renderVerificationEmail(string $verificationUrl, User $user): string;
}
