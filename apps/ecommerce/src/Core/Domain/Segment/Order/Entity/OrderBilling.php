<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Order\Traits\OrderTrait,
    Shared\Traits\Details\AddressTrait,
    Shared\Traits\Identity\IdTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'order_billings')]
class OrderBilling
{
    use IdTrait;
    use OrderTrait;
    use AddressTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\OneToOne(targetEntity: Order::class, inversedBy: 'billing')]
    #[ORM\JoinColumn(
        name: 'order_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private Order $order;
}
