<?php

declare(strict_types=1);

namespace App\Core\Ports\Web\Auth\Renderer;

use App\Core\Domain\Segment\User\Entity\User;

interface ResetPasswordEmailRendererContract
{
    public function renderResetPasswordEmail(User $user): string;
}
