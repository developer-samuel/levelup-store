<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Input;

trait ReviewRatingInput
{
    public string $reviewId;
    public ?string $type;
}
