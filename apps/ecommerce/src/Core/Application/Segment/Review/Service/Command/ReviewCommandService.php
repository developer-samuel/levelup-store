<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Service\Command;

use Packages\Kit\{
    Assertion\Domain\Product\Variant\ProductVariantAssertion,
    Assertion\Domain\Review\ReviewAssertion
};

use App\Core\Domain\{
    Shared\Exception\AccessDeniedException,
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
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private ReviewRepositoryContract $reviewRepository,
        private ProductVariantRepositoryContract $variantRepository,
        private ReviewQueryContract $reviewQuery,
    ) {}

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

    public function remove(int $id, User $user): void
    {
        $review = $this->reviewRepository->findById($id);
        ReviewAssertion::assertExists($review);

        if (!$review->isOwnedBy($user)) {
            throw new AccessDeniedException('You are not allowed to delete this review.');
        }

        $this->entityPersistence->remove($review, true);
    }

    private function createReview(User $user, ProductVariant $variant, int $value): Review
    {
        return (new Review())
            ->setUser($user)
            ->setVariant($variant)
            ->setValue($value)
            ->setType(ReviewType::RATING);
    }

    /** @param string[] $details */
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

    private function buildDetail(Review $review, string $body, ReviewDetailType $type): ReviewDetail
    {
        return (new ReviewDetail())
            ->setReview($review)
            ->setBody(mb_substr($body, 0, 80))
            ->setType($type);
    }
}
