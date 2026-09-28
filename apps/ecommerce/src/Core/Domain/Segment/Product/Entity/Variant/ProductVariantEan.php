<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Entity\Variant;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Product\Enum\Variant\ProductVariantEanStatus,
    Segment\Product\Traits\Variant\ProductVariantTrait,
    Shared\Traits\Identity\CodeTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'product_variant_eans')]
#[ORM\HasLifecycleCallbacks]
class ProductVariantEan
{
    use IdTrait;
    use ProductVariantTrait;
    use CodeTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ProductVariant::class, inversedBy: 'eans')]
    #[ORM\JoinColumn(
        name: 'variant_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private ProductVariant $variant;

    #[ORM\Column(type: 'string', unique: true, length: 13)]
    private string $code;

    #[ORM\Column(
        type: 'string',
        length: 10,
        enumType: ProductVariantEanStatus::class,
        options: ['default' => ProductVariantEanStatus::ACTIVE->value],
    )]
    private ProductVariantEanStatus $status = ProductVariantEanStatus::ACTIVE;

    /**
     * @return ProductVariantEanStatus
    */
    public function getStatus(): ProductVariantEanStatus
    {
        return $this->status;
    }

    /**
     * @param ProductVariantEanStatus $status
     *
     * @return self
    */
    public function setStatus(ProductVariantEanStatus $status): self
    {
        $this->status = $status;
        return $this;
    }
}
