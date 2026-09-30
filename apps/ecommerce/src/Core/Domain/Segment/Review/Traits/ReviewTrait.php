<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Traits;

use App\Core\Domain\Segment\Review\Entity\Review;

/**
 * @property Review $review
*/
trait ReviewTrait
{
    /**
     * @return Review
    */
    public function getReview(): Review
    {
        return $this->review;
    }

    /**
     * @param Review $review
     * 
     * @return self
    */
    public function setReview(Review $review): self
    {
        $this->review = $review;
        return $this;
    }
}
