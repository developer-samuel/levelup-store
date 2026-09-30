<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Order\Traits\OrderTrait,
    Shared\Traits\Details\PriceTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'order_payments')]
#[ORM\HasLifecycleCallbacks]
class OrderPayment
{
    use IdTrait;
    use OrderTrait;
    use PriceTrait;
    use CreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\OneToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(
        name: 'order_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private Order $order;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $transactionUnique;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price = 0.00;

    /**
     * @return string
    */
    public function getTransactionUnique(): string
    {
        return $this->transactionUnique;
    }

    /**
     * @param string $transactionUnique
     *
     * @return self
    */
    public function setTransactionUnique(string $transactionUnique): self
    {
        $this->transactionUnique = $transactionUnique;
        return $this;
    }
}
