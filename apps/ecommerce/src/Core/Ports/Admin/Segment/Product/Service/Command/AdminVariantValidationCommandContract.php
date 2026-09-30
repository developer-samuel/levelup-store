<?php

declare(strict_types=1);

namespace App\Core\Ports\Admin\Segment\Product\Service\Command;

interface AdminVariantValidationCommandContract
{
    /**
     * @param object $payload
     *
     * @return int
     *
     * @throws \DomainException
    */
    public function extractAndValidateId(object $payload, string $field = 'id'): int;

    /**
     * @param object $payload
     *
     * @return int
     *
     * @throws \DomainException
    */
    public function extractAndValidateVariantId(object $payload): int;
}
