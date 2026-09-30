<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Payload;

final readonly class ReviewCreatePayload
{
    /**
     * @param int $variantId
     * @param int $value
     * @param string[] $positives
     * @param string[] $negatives
     * @param string|null $body
    */
    public function __construct(
        public int $variantId,
        public int $value,
        public array $positives = [],
        public array $negatives = [],
        public ?string $body = null,
    ) {}
}
