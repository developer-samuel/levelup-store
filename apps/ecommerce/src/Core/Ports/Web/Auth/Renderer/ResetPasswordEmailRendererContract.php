<?php

declare(strict_types=1);

namespace App\Core\Ports\Web\Auth\Renderer;

use App\Core\Domain\Segment\User\Entity\User;

interface ResetPasswordEmailRendererContract
{
    /**
     * @param User $user
     *
     * @return string
    */
    public function renderResetPasswordEmail(User $user): string;
}
