<?php

declare(strict_types=1);

namespace App\Core\Ports\Web\Auth\Renderer;

use App\Core\Domain\Segment\User\Entity\User;

interface VerificationEmailRendererContract
{
    public function renderVerificationEmail(string $verificationUrl, User $user): string;
}
