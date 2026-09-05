<?php

declare(strict_types=1);

namespace Database\Seeds\Builders;

use Doctrine\Persistence\ObjectManager;

use Database\{
    Seeds\Factories\Review\ReviewFactory,
    Seeds\Factories\Review\ReviewRatingFactory
};

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Review\Entity\Review,
    Segment\Review\Entity\ReviewDetail,
    Segment\Review\Enum\ReviewDetailType,
    Segment\Review\Enum\ReviewType,
    Segment\User\Entity\User
};

trait ReviewBuilder
{
    use ReviewFactory;
    use ReviewRatingFactory;

    private const LOREM_WORDS_FILE = __DIR__ . '/../../data/words/lorem_words.json';
    private const FEEDBACK_CHANCE = 70;
    private const RATING_CHANCE = 60;

    /**
     * @param ObjectManager $manager
     * @param ProductVariant[] $variants
     * @param User $user
     *
     * @return Review[]
    */
    private function createReviewsWithRatings(ObjectManager $manager, array $variants, User $user): array
    {
        $reviews = [];

        foreach ($variants as $variant) {
            $reviewEntity = new Review();
            $details = $this->generateDetails($reviewEntity);

            $value = $this->generateReviewValue(null, $details);

            $review = $this->createAndPersistReviewWithDetails($manager, $variant, $user, $value, $details);

            $this->maybePersistRandomRating($manager, $review, $user);

            $reviews[] = $review;
        }

        return $reviews;
    }

    /**
     * @param float|null $previousValue
     * @param ReviewDetail[] $details
     *
     * @return float
    */
    private function generateReviewValue(?float $previousValue, array $details = []): float
    {
        if (!empty($details)) {
            return $this->generateReviewValueBasedOnDetails($details);
        }

        if ($previousValue === null) {
            return $this->randomFloat(3.0, 5.0, 1);
        }

        [$min, $max] = $this->getMinMaxPossibleValues($previousValue);

        return $this->randomFloat($min, $max, 1);
    }

    /**
     * @param ObjectManager $manager
     * @param ProductVariant $variant
     * @param User $user
     * @param float $value
     * @param ReviewDetail[] $details
     *
     * @return Review
    */
    private function createAndPersistReviewWithDetails(
        ObjectManager $manager,
        ProductVariant $variant,
        User $user,
        float $value,
        array $details,
    ): Review {
        [$body, $type] = $this->generateReviewBodyAndType();

        $review = $this->createReview($variant, $user, $value, $body, $type);
        $manager->persist($review);

        foreach ($details as $detail) {
            $detail->setReview($review);
            $manager->persist($detail);
        }

        return $review;
    }

    /**
     * @return array{0: string|null, 1: ReviewType}
    */
    private function generateReviewBodyAndType(): array
    {
        $body = rand(0, 100) < self::FEEDBACK_CHANCE ? $this->randomSentence() : null;
        $type = $body !== null ? ReviewType::FEEDBACK : ReviewType::RATING;

        return [$body, $type];
    }

    /**
     * @param ReviewDetail[] $details
     *
     * @return float
    */
    private function generateReviewValueBasedOnDetails(array $details): float
    {
        [$positive, $negative] = $this->countPositiveNegative($details);

        if ($positive === 0 && $negative === 0) {
            return $this->randomFloat(3, 5, 1);
        }

        $ratio = $this->calculatePositiveRatio($positive, $negative);

        return $this->mapRatioToValue($ratio);
    }

    /**
     * @param float $previousValue
     *
     * @return float[]
    */
    private function getMinMaxPossibleValues(float $previousValue): array
    {
        $possible = $this->getPossibleValues($previousValue);

        $min = !empty($possible) ? (float) min($possible) : 1.0;
        $max = !empty($possible) ? (float) max($possible) : 5.0;

        return [$min, $max];
    }

    /**
     * @param ReviewDetail[] $details
     *
     * @return int[] [0 => positive, 1 => negative]
    */
    private function countPositiveNegative(array $details): array
    {
        $positive = 0;
        $negative = 0;

        foreach ($details as $detail) {
            if ($detail->getType() === ReviewDetailType::POSITIVE) {
                $positive++;
            } else {
                $negative++;
            }
        }

        return [$positive, $negative];
    }

    /**
     * @param int $positive
     * @param int $negative
     *
     * @return float
    */
    private function calculatePositiveRatio(int $positive, int $negative): float
    {
        $total = $positive + $negative;

        return $total > 0 ? $positive / $total : 0;
    }

    /**
     * @param float $ratio
     *
     * @return float
    */
    private function mapRatioToValue(float $ratio): float
    {
        return match (true) {
            $ratio === 1.0 => 5.0,
            $ratio >= 0.7  => 4.0,
            $ratio >= 0.5  => 3.0,
            $ratio >= 0.3  => 2.0,
            default        => 1.0,
        };
    }

    /**
     * @param float $previousValue
     *
     * @return int[]
    */
    private function getPossibleValues(float $previousValue): array
    {
        $possible = [];

        $floor = max(1, (int) floor($previousValue));
        $ceil = min(5, (int) ceil($previousValue));

        $possible[] = $floor;
        if ($ceil !== $floor) {
            $possible[] = $ceil;
        }

        if ($floor > 1) {
            $possible[] = $floor - 1;
        }

        if ($ceil < 5) {
            $possible[] = $ceil + 1;
        }

        return $this->filterAndUniqueValues($possible);
    }

    /**
     * @param int[] $values
     *
     * @return int[]
    */
    private function filterAndUniqueValues(array $values): array
    {
        return array_filter(
            array_unique($values),
            static fn(int $v): bool => $v >= 1 && $v <= 5,
        );
    }

    /**
     * @param ObjectManager $manager
     * @param Review $review
     * @param User $user
     *
     * @return void
    */
    private function maybePersistRandomRating(ObjectManager $manager, Review $review, User $user): void
    {
        if (rand(0, 100) < self::RATING_CHANCE) {
            $rating = $this->createRandomRating($review, $user);

            $manager->persist($rating);
        }
    }

    /**
     * @param Review $review
     *
     * @return ReviewDetail[]
    */
    private function generateDetails(Review $review): array
    {
        $details = [];
        $count = rand(1, 3);

        while ($count-- > 0) {
            $details[] = (new ReviewDetail())
                ->setReview($review)
                ->setBody($this->randomSentence())
                ->setType(rand(0, 1) ? ReviewDetailType::POSITIVE : ReviewDetailType::NEGATIVE);
        }

        return $details;
    }

    /**
     * @param float $min
     * @param float $max
     * @param int $decimals
     *
     * @return float
    */
    private function randomFloat(float $min, float $max, int $decimals = 1): float
    {
        $scale = pow(10, $decimals);

        return mt_rand((int) ($min * $scale), (int) ($max * $scale)) / $scale;
    }

    /**
     * @param int $wordCount
     *
     * @return string
    */
    private function randomSentence(int $wordCount = 5): string
    {
        $words = $this->loadLoremWords();
        $sentence = [];

        for ($i = 0; $i < $wordCount; $i++) {
            $sentence[] = $words[array_rand($words)];
        }

        return ucfirst(implode(' ', $sentence)) . '.';
    }

    /**
     * @return string[]
     *
     * @throws \RuntimeException
    */
    private function loadLoremWords(): array
    {
        $json = file_get_contents(self::LOREM_WORDS_FILE);

        if ($json === false) {
            throw new \RuntimeException(sprintf('Failed to read lorem words file: %s', self::LOREM_WORDS_FILE));
        }

        $words = json_decode($json, true);

        if (!is_array($words)) {
            throw new \RuntimeException(sprintf('Invalid JSON in lorem words file: %s', self::LOREM_WORDS_FILE));
        }

        return $this->filterAndCastToString($words);
    }

    /**
     * @param mixed[] $values
     *
     * @return string[]
    */
    private function filterAndCastToString(array $values): array
    {
        $strings = [];
        foreach ($values as $value) {
            if (is_scalar($value) || $value === null) {
                $strings[] = (string) $value;
            }
        }

        return $strings;
    }
}
