<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Cart\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Traits\Variant\ProductVariantTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'cart_items')]
#[ORM\HasLifecycleCallbacks]
class CartItem
{
    use ProductVariantTrait;
    use CreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'items')]
    #[ORM\JoinColumn(
        name: 'cart_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private Cart $cart;

    #[ORM\ManyToOne(targetEntity: ProductVariant::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ProductVariant $variant;

    public function __construct(
        Cart $cart,
        ProductVariant $variant,
    ) {
        $this->cart = $cart;
        $this->variant = $variant;
    }

    /**
     * @return int|null
    */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return self
    */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Cart|null
    */
    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    /**
     * @param ProductVariant $variant
     *
     * @return bool
    */
    public function hasVariant(ProductVariant $variant): bool
    {
        return $this->variant->getId() === $variant->getId();
    }
}
