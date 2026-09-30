<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Traits\UserTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\State\ExpiresTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait
};

use App\Shared\Traits\Identity\TokenTrait;

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens')]
#[ORM\HasLifecycleCallbacks]
class RefreshToken
{
    use IdTrait;
    use TokenTrait;
    use UserTrait;
    use ExpiresTrait;
    use CreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;
}
