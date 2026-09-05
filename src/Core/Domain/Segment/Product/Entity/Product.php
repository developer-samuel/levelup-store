<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Entity;

use Doctrine\{
    Common\Collections\ArrayCollection,
    Common\Collections\Collection,
    ORM\Mapping as ORM
};

use App\Core\Domain\{
    Segment\Brand\Brand,
    Segment\Category\Entity\Category,
    Segment\Category\Traits\CategoryTrait,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Traits\ProductCoreTrait,
    Segment\Product\Traits\ProductSubtypeCollectionTrait,
    Segment\Type\Entity\Type,
    Segment\Type\Traits\TypeTrait,
    Shared\Traits\Details\NameTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ORM\HasLifecycleCallbacks]
class Product
{
    use IdTrait;
    use CategoryTrait;
    use TypeTrait;
    use NameTrait;
    use ProductSubtypeCollectionTrait;
    use ProductCoreTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(
        name: 'category_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: Type::class)]
    #[ORM\JoinColumn(
        name: 'type_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    private Type $type;

    #[ORM\ManyToOne(targetEntity: Brand::class)]
    #[ORM\JoinColumn(
        name: 'brand_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    private Brand $brand;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $catalogCode;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;

    /**
     * @var Collection<int, ProductVariant>
    */
    #[ORM\OneToMany(
        mappedBy: 'product',
        targetEntity: ProductVariant::class,
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: true,
        fetch: 'EAGER',
    )]
    private Collection $variants;

    /**
     * @var Collection<int, ProductSubtype>
    */
    #[ORM\OneToMany(
        mappedBy: 'product',
        targetEntity: ProductSubtype::class,
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: true,
        fetch: 'LAZY',
    )]
    private Collection $subtypes;

    public function __construct()
    {
        $this->variants = new ArrayCollection();
        $this->subtypes = new ArrayCollection();
    }
}
