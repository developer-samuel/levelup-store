<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Entity;

use Doctrine\{
    Common\Collections\ArrayCollection,
    Common\Collections\Collection,
    ORM\Mapping as ORM
};

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Traits\Variant\ProductVariantTrait,
    Segment\Review\Enum\ReviewType,
    Segment\Review\Traits\ReviewCoreTrait,
    Segment\User\Entity\User,
    Segment\User\Traits\UserTrait,
    Segment\User\Traits\UserOwnedTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'reviews')]
#[ORM\HasLifecycleCallbacks]
class Review
{
    use IdTrait;
    use UserTrait;
    use ProductVariantTrait;
    use ReviewCoreTrait;
    use UserOwnedTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

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

    #[ORM\ManyToOne(targetEntity: ProductVariant::class)]
    #[ORM\JoinColumn(
        name: 'variant_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private ProductVariant $variant;

    #[ORM\Column(type: 'decimal', precision: 2, scale: 1, options: ['default' => 5.0])]
    private float $value;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    private ?string $body = null;

    #[ORM\Column(
        type: 'string',
        length: 10,
        enumType: ReviewType::class,
        options: ['default' => ReviewType::RATING->value],
    )]
    private ReviewType $type = ReviewType::RATING;

    /**
     * @var Collection<int, ReviewDetail>
    */
    #[ORM\OneToMany(
        mappedBy: 'review',
        targetEntity: ReviewDetail::class,
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: true,
        fetch: 'EAGER',
    )]
    private Collection $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }

    /**
     * @return string|null
    */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     *
     * @return self
    */
    public function setBody(?string $body): self
    {
        $this->body = $body;
        return $this;
    }
}
