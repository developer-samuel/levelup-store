<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Service\Command;

use Kit\Utils\Shared\DataSanitizer;

use App\Core\Ports\Admin\Segment\Product\Service\Command\AdminVariantValidationCommandContract;

class AdminVariantValidationCommandService implements AdminVariantValidationCommandContract
{
    /**
     * @param object $payload
     *
     * @return int
     *
     * @throws \DomainException
    */
    public function extractAndValidateId(object $payload, string $field = 'id'): int
    {
        $id = DataSanitizer::sanitizeInt($payload->$field ?? null);

        return $this->validatePositiveInt($id, 'ID');
    }

    /**
     * @param object $payload
     *
     * @return int
     *
     * @throws \DomainException
    */
    public function extractAndValidateVariantId(object $payload): int
    {
        $variantId = DataSanitizer::sanitizeInt($payload->variantId ?? null);

        return $this->validatePositiveInt($variantId, 'Variant ID');
    }

    /**
     * @param mixed $value
     * @param string $name
     *
     * @return int
     *
     * @throws \DomainException
    */
    private function validatePositiveInt(mixed $value, string $name): int
    {
        if (!is_int($value) || $value <= 0) {
            throw new \DomainException(sprintf('Invalid %s.', $name));
        }

        return $value;
    }
}
