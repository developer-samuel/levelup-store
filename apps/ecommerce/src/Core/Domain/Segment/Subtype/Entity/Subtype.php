<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Subtype\Entity;

use Doctrine\{
    Common\Collections\ArrayCollection,
    Common\Collections\Collection,
    ORM\Mapping as ORM
};

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Category\Traits\CategoryTrait,
    Segment\Product\Entity\Product,
    Segment\Product\Entity\ProductSubtype,
    Segment\Product\Traits\ProductCollectionTrait,
    Segment\Subtype\Traits\SubtypeCoreTrait,
    Segment\Type\Entity\Type,
    Segment\Type\Traits\TypeTrait,
    Shared\Traits\Details\NameTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'subtypes')]
#[ORM\HasLifecycleCallbacks]
class Subtype
{
    use IdTrait;
    use NameTrait;
    use TypeTrait;
    use CategoryTrait;
    use ProductCollectionTrait;
    use SubtypeCoreTrait;
    use CreatedTimestampTrait;

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

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $name;

    /**
     * @var Collection<int, Product>
    */
    #[ORM\OneToMany(mappedBy: 'subtype', targetEntity: Product::class)]
    private Collection $products;

    /**
     * @var Collection<int, ProductSubtype>
    */
    #[ORM\OneToMany(mappedBy: 'subtype', targetEntity: ProductSubtype::class)]
    private Collection $productSubtypes;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->productSubtypes = new ArrayCollection();
    }
}
