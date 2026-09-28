<?php

declare(strict_types=1);

namespace App\Core\Ports\Security;

use App\Core\Domain\Segment\User\Entity\User;

interface SecurityPolicyContract
{
    /**
     * @return User
    */
    public function checkAccess(): User;

    /**
     * @return User
    */
    public function checkIfEmailVerified(): User;

    /**
     * @return User
    */
    public function checkAdminAccess(): User;
}
