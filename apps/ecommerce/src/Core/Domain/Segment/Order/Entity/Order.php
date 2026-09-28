<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Entity;

use Doctrine\{
    Common\Collections\ArrayCollection,
    Common\Collections\Collection,
    ORM\Mapping as ORM
};

use App\Core\Domain\{
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Enum\OrderStatus,
    Segment\User\Entity\User,
    Segment\User\Traits\UserTrait,
    Segment\User\Traits\UserOwnedTrait,
    Shared\Traits\Collection\ItemCollectionTrait,
    Shared\Traits\Details\PriceTrait,
    Shared\Traits\Identity\CodeTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/**
 * @SuppressWarnings("UnusedPrivateField")
 *
 * @property Collection<int, OrderItem> $items
*/
#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ORM\HasLifecycleCallbacks]
class Order
{
    use IdTrait;
    use UserTrait;
    use CodeTrait;
    use PriceTrait;
    use UserOwnedTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    /**
     * @use ItemCollectionTrait<OrderItem>
    */
    use ItemCollectionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private User $user;

    #[ORM\Column(type: 'string', unique: true, length: 20, nullable: false)]
    private string $code;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(
        type: 'string',
        length: 10,
        nullable: false,
        enumType: OrderPaymentMethod::class,
        options: ['default' => OrderPaymentMethod::CARD->value],
    )]
    private OrderPaymentMethod $payment = OrderPaymentMethod::CARD;

    #[ORM\Column(
        type: 'string',
        length: 10,
        enumType: OrderStatus::class,
        options: ['default' => OrderStatus::PROCESSED->value],
    )]
    private OrderStatus $status = OrderStatus::PROCESSED;

    #[ORM\Column(name: 'send_shipping', type: 'boolean', options: ['default' => false])]
    private bool $sendShipping = false;

    #[ORM\OneToOne(
        mappedBy: 'order',
        targetEntity: OrderPayment::class,
        orphanRemoval: true,
    )]
    private ?OrderPayment $orderPayment = null;

    #[ORM\OneToOne(
        mappedBy: 'order',
        targetEntity: OrderPersonal::class,
        orphanRemoval: true,
    )]
    private ?OrderPersonal $personal = null;

    #[ORM\OneToOne(
        mappedBy: 'order',
        targetEntity: OrderBilling::class,
        orphanRemoval: true,
    )]
    private ?OrderBilling $billing = null;

    #[ORM\OneToOne(
        mappedBy: 'order',
        targetEntity: OrderShipping::class,
        orphanRemoval: true,
    )]
    private ?OrderShipping $shipping = null;

    /**
     * @var Collection<int, OrderItem>
    */
    #[ORM\OneToMany(
        mappedBy: 'order',
        targetEntity: OrderItem::class,
        cascade: ['remove'],
    )]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return OrderPaymentMethod
    */
    public function getPayment(): OrderPaymentMethod
    {
        return $this->payment;
    }

    /**
     * @param OrderPaymentMethod $payment
     *
     * @return self
    */
    public function setPayment(OrderPaymentMethod $payment): self
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * @return OrderStatus
    */
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @param OrderStatus $status
     *
     * @return self
    */
    public function setStatus(OrderStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return bool
    */
    public function getSendShipping(): bool
    {
        return $this->sendShipping;
    }

    /**
     * @param bool $sendShipping
     *
     * @return void
    */
    public function setSendShipping(bool $sendShipping): void
    {
        $this->sendShipping = $sendShipping;
    }

    /**
     * @return OrderPayment|null
    */
    public function getOrderPayment(): ?OrderPayment
    {
        return $this->orderPayment;
    }

    /**
     * @param OrderPayment|null $orderPayment
     *
     * @return self
    */
    public function setOrderPayment(?OrderPayment $orderPayment): self
    {
        $this->orderPayment = $orderPayment;
        return $this;
    }

    /**
     * @return bool
    */
    public function hasPayment(): bool
    {
        return $this->orderPayment !== null;
    }

    /**
     * @return bool
    */
    public function isCashPayment(): bool
    {
        return $this->getPayment() === OrderPaymentMethod::CASH;
    }

    /**
     * @return bool
    */
    public function isCardPayment(): bool
    {
        return $this->getPayment() === OrderPaymentMethod::CARD;
    }

    /**
     * @return OrderPersonal|null
    */
    public function getPersonal(): ?OrderPersonal
    {
        return $this->personal;
    }

    /**
     * @param OrderPersonal|null $personal
     *
     * @return self
    */
    public function setPersonal(?OrderPersonal $personal): self
    {
        $this->personal = $personal;
        return $this;
    }

    /**
     * @return OrderBilling|null
    */
    public function getBilling(): ?OrderBilling
    {
        return $this->billing;
    }

    /**
     * @param OrderBilling|null $billing
     *
     * @return self
    */
    public function setBilling(?OrderBilling $billing): self
    {
        $this->billing = $billing;
        return $this;
    }

    /**
     * @return OrderShipping|null
    */
    public function getShipping(): ?OrderShipping
    {
        return $this->shipping;
    }

    /**
     * @param OrderShipping|null $shipping
     *
     * @return self
    */
    public function setShipping(?OrderShipping $shipping): self
    {
        $this->shipping = $shipping;
        return $this;
    }
}
