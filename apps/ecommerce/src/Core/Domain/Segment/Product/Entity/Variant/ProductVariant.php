<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Entity\Variant;

use Doctrine\{
    Common\Collections\ArrayCollection,
    Common\Collections\Collection,
    ORM\Mapping as ORM
};

use App\Core\Domain\{
    Segment\Product\Entity\Product,
    Segment\Product\Enum\Variant\ProductVariantStatus,
    Segment\Product\Traits\ProductTrait,
    Segment\Product\Traits\Variant\ProductVariantCoreTrait,
    Shared\Traits\Details\NameTrait,
    Shared\Traits\Details\PriceTrait,
    Shared\Traits\Details\UrlTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'product_variants')]
#[ORM\HasLifecycleCallbacks]
class ProductVariant
{
    use IdTrait;
    use ProductTrait;
    use ProductVariantCoreTrait;
    use NameTrait;
    use PriceTrait;
    use UrlTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'variants')]
    #[ORM\JoinColumn(
        name: 'product_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    private Product $product;

    #[ORM\Column(type: 'string', length: 100, unique: true, nullable: false)]
    private string $sku;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price = 0.00;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $url;

    /**
     * @var Collection<int, ProductVariantDescription>
    */
    #[ORM\OneToMany(
        targetEntity: ProductVariantDescription::class,
        mappedBy: 'variant',
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: true,
        fetch: 'LAZY',
    )]
    private Collection $descriptions;

    /**
     * @var Collection<int, ProductVariantEan>
    */
    #[ORM\OneToMany(
        mappedBy: 'variant',
        targetEntity: ProductVariantEan::class,
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: false,
        fetch: 'LAZY',
    )]
    private Collection $eans;

    /**
     * @var Collection<int, ProductVariantImage>
    */
    #[ORM\OneToMany(
        targetEntity: ProductVariantImage::class,
        mappedBy: 'variant',
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: true,
        fetch: 'LAZY',
    )]
    private Collection $images;

    #[ORM\OneToOne(
        mappedBy: 'variant',
        targetEntity: ProductVariantDiscount::class,
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: true,
        fetch: 'LAZY',
    )]
    private ?ProductVariantDiscount $discount = null;

    #[ORM\Column(
        type: 'string',
        length: 10,
        enumType: ProductVariantStatus::class,
        options: ['default' => ProductVariantStatus::AVAILABLE->value],
    )]
    private ProductVariantStatus $status = ProductVariantStatus::AVAILABLE;

    #[ORM\OneToOne(
        mappedBy: 'variant',
        targetEntity: ProductVariantStock::class,
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: true,
        fetch: 'LAZY',
    )]
    private ?ProductVariantStock $stock = null;

    public function __construct()
    {
        $this->descriptions = new ArrayCollection();
        $this->eans = new ArrayCollection();
        $this->images = new ArrayCollection();
    }
}
