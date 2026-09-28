<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Review\Enum\ReviewDetailType,
    Segment\Review\Traits\ReviewTrait,
    Shared\Traits\Details\BodyTrait,
    Shared\Traits\Identity\IdTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'review_details')]
class ReviewDetail
{
    use IdTrait;
    use BodyTrait;
    use ReviewTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Review::class, inversedBy: 'details')]
    #[ORM\JoinColumn(
        name: 'review_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private Review $review;

    #[ORM\Column(type: 'text', length: 80, nullable: false)]
    private string $body;

    #[ORM\Column(
        type: 'string',
        length: 10,
        enumType: ReviewDetailType::class,
        options: ['default' => ReviewDetailType::POSITIVE->value],
    )]
    private ReviewDetailType $type = ReviewDetailType::POSITIVE;

    /**
     * @return ReviewDetailType
    */
    public function getType(): ReviewDetailType
    {
        return $this->type;
    }

    /**
     * @param ReviewDetailType $type
     *
     * @return self
    */
    public function setType(ReviewDetailType $type): self
    {
        $this->type = $type;
        return $this;
    }
}
