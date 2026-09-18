<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Order\Traits\OrderTrait,
    Segment\User\Traits\UserEmailTrait,
    Segment\User\Traits\UserNameTrait,
    Shared\Traits\Identity\IdTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'order_personals')]
class OrderPersonal
{
    use IdTrait;
    use UserEmailTrait;
    use UserNameTrait;
    use OrderTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\OneToOne(targetEntity: Order::class, inversedBy: 'personal')]
    #[ORM\JoinColumn(
        name: 'order_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private Order $order;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $email;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $lastName;
}
