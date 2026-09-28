<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Entity;

use Symfony\{
    Component\Security\Core\User\PasswordAuthenticatedUserInterface,
    Component\Security\Core\User\UserInterface
};

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\User\Enum\UserRole,
    Segment\User\Traits\UserCoreTrait,
    Segment\User\Traits\UserEmailTrait,
    Segment\User\Traits\UserNameTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\State\SoftDeletesTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: "users")]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use IdTrait;
    use UserEmailTrait;
    use UserNameTrait;
    use UserCoreTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;
    use SoftDeletesTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $password;

    #[ORM\Column(
        type: 'string',
        enumType: UserRole::class,
        options: ['default' => UserRole::USER->value],
    )]
    private UserRole $role = UserRole::USER;

    #[ORM\Column(name: "use_shipping", type: 'boolean', options: ["default" => false])]
    private bool $useShipping = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $emailVerifiedAt = null;

    #[ORM\OneToOne(targetEntity: UserBilling::class, mappedBy: 'user')]
    private ?UserBilling $billing = null;

    #[ORM\OneToOne(targetEntity: UserShipping::class, mappedBy: 'user')]
    private ?UserShipping $shipping = null;
}
