<?php

declare(strict_types=1);

namespace App\Core\Ports\Security;

use App\Core\Domain\Segment\User\Entity\User;

interface SecurityPolicyContract
{
    public function checkAccess(): User;
    public function checkIfEmailVerified(): User;
    public function checkAdminAccess(): User;
}
