<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Traits;

use Doctrine\Common\Collections\Collection;

use App\Core\Domain\{
    Segment\Review\Entity\ReviewDetail,
    Segment\Review\Enum\ReviewType
};

trait ReviewCoreTrait
{
    /** @return Collection<int, ReviewDetail> */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    /** @param Collection<int, ReviewDetail> $details */
    public function setDetails(Collection $details): self
    {
        $this->details = $details;
        return $this;
    }

    public function getType(): ReviewType
    {
        return $this->type;
    }

    public function setType(ReviewType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function applyBody(?string $body): bool
    {
        $text = trim($body ?? '');
        $hasBody = $text !== '';

        if ($hasBody) {
            $this->body = mb_substr($text, 0, 250);
        }

        return $hasBody;
    }

    public function recalculateType(): void
    {
        $this->type = ($this->body !== null && trim($this->body) !== '') || !$this->details->isEmpty()
            ? ReviewType::FEEDBACK
            : ReviewType::RATING;
    }
}
