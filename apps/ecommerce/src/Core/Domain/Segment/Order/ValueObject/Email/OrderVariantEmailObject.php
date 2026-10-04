<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Email;

final readonly class OrderVariantEmailObject
{
    public function __construct(
        public string $name,
        public string $url,
    ) {}
}
