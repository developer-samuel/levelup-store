<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Payload;

final readonly class ReviewCreatePayload
{
    /**
     * @param string[] $positives
     * @param string[] $negatives
    */
    public function __construct(
        public int $variantId,
        public int $value,
        public array $positives = [],
        public array $negatives = [],
        public ?string $body = null,
    ) {}
}
