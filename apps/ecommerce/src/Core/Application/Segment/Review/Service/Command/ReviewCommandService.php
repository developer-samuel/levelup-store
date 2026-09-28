<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Service\Command;

use Kit\{
    Assertion\Domain\Product\Variant\ProductVariantAssertion,
    Assertion\Domain\Review\ReviewAssertion
};

use App\Core\Domain\{
    Shared\Exception\AccessDeniedException,
    Shared\Exception\NotFoundException,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Review\Payload\ReviewCreatePayload,
    Segment\Review\Entity\Review,
    Segment\Review\Entity\ReviewDetail,
    Segment\Review\Enum\ReviewDetailType,
    Segment\Review\Enum\ReviewType,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\Review\Repository\ReviewRepositoryContract,
    Segment\Review\Service\Command\ReviewCommandContract,
    Segment\Review\Service\Query\ReviewQueryContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class ReviewCommandService implements ReviewCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param ReviewRepositoryContract $reviewRepository
     * @param ProductVariantRepositoryContract $variantRepository
     * @param ReviewQueryContract $reviewQuery
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private ReviewRepositoryContract $reviewRepository,
        private ProductVariantRepositoryContract $variantRepository,
        private ReviewQueryContract $reviewQuery,
    ) {}

    /**
     * @param ReviewCreatePayload $payload
     * @param User $user
     *
     * @return void
    */
    public function add(ReviewCreatePayload $payload, User $user): void
    {
        $variant = $this->variantRepository->findById($payload->variantId);
        ProductVariantAssertion::assertExists($variant);

        $review = $this->createReview($user, $variant, $payload->value);

        $review->applyBody($payload->body);

        $this->createDetails($review, $payload->positives, ReviewDetailType::POSITIVE);
        $this->createDetails($review, $payload->negatives, ReviewDetailType::NEGATIVE);

        $review->recalculateType();

        $this->entityPersistence->persist($review, true);
    }

    /**
     * @param int $id
     * @param User $user
     *
     * @return void
     *
     * @throws NotFoundException
     * @throws AccessDeniedException
    */
    public function remove(int $id, User $user): void
    {
        $review = $this->reviewRepository->findById($id);
        ReviewAssertion::assertExists($review);

        if (!$review->isOwnedBy($user)) {
            throw new AccessDeniedException('You are not allowed to delete this review.');
        }

        $this->entityPersistence->remove($review, true);
    }

    /**
     * @param User $user
     * @param ProductVariant $variant
     * @param int $value
     *
     * @return Review
    */
    private function createReview(User $user, ProductVariant $variant, int $value): Review
    {
        return (new Review())
            ->setUser($user)
            ->setVariant($variant)
            ->setValue($value)
            ->setType(ReviewType::RATING);
    }

    /**
     * @param Review $review
     * @param string[] $details
     * @param ReviewDetailType $type
     *
     * @return void
    */
    private function createDetails(Review $review, array $details, ReviewDetailType $type): void
    {
        $details = $this->reviewQuery->limitDetails($details);

        foreach ($details as $text) {
            $trimmed = trim($text ?? '');
            if ($trimmed === '') {
                continue;
            }

            $detail = $this->buildDetail($review, $trimmed, $type);

            $review->getDetails()->add($detail);
        }
    }

    /**
     * @param Review $review
     * @param string $body
     * @param ReviewDetailType $type
     *
     * @return ReviewDetail
    */
    private function buildDetail(Review $review, string $body, ReviewDetailType $type): ReviewDetail
    {
        return (new ReviewDetail())
            ->setReview($review)
            ->setBody(mb_substr($body, 0, 80))
            ->setType($type);
    }
}
