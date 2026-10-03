<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Traits;

use App\Core\Domain\Segment\Review\Entity\Review;

trait ReviewTrait
{
    public function getReview(): Review
    {
        return $this->review;
    }

    public function setReview(Review $review): self
    {
        $this->review = $review;
        return $this;
    }
}
