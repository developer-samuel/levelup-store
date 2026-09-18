<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\User\Traits\UserTrait,
    Shared\Traits\Details\AddressTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: "user_shippings")]
#[ORM\HasLifecycleCallbacks]
class UserShipping
{
    use IdTrait;
    use UserTrait;
    use AddressTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'shipping')]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private User $user;
}
