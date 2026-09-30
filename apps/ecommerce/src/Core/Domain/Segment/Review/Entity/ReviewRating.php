<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Review\Enum\ReviewRatingType,
    Segment\Review\Traits\ReviewTrait,
    Segment\User\Entity\User,
    Segment\User\Traits\UserTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'review_ratings')]
#[ORM\HasLifecycleCallbacks]
class ReviewRating
{
    use IdTrait;
    use ReviewTrait;
    use UserTrait;
    use CreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Review::class, inversedBy: 'ratings')]
    #[ORM\JoinColumn(
        name: 'review_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private Review $review;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private User $user;

    #[ORM\Column(
        type: 'string',
        length: 10,
        enumType: ReviewRatingType::class,
        options: ['default' => ReviewRatingType::LIKE->value],
    )]
    private ReviewRatingType $type = ReviewRatingType::LIKE;

    /**
     * @return ReviewRatingType
    */
    public function getType(): ReviewRatingType
    {
        return $this->type;
    }

    /**
     * @param ReviewRatingType $type
     *
     * @return self
    */
    public function setType(ReviewRatingType $type): self
    {
        $this->type = $type;
        return $this;
    }
}
