<?php

declare(strict_types=1);

namespace Tests\Support\Stub;

use App\Core\Domain\Segment\User\Entity\User;

trait UserStub
{
    private function createUserWithId(int $id): User
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($id);

        return $user;
    }
}
