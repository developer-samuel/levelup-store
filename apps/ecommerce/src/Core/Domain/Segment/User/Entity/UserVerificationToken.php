<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\User\Traits\UserTrait,
    Shared\Traits\State\ExpiresTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait
};

use App\Shared\Traits\Identity\TokenTrait;

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'user_verification_tokens')]
#[ORM\HasLifecycleCallbacks]
class UserVerificationToken
{
    use IdTrait;
    use UserTrait;
    use TokenTrait;
    use ExpiresTrait;
    use CreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id',
        onDelete: 'CASCADE',
        nullable: false,
    )]
    private User $user;
}
