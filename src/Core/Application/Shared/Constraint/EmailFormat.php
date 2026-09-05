<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Constraint;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Shared\Validator\FormatEmailValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class EmailFormat extends Constraint
{
    public string $message = 'Invalid email format.';

    /**
     * @return string
    */
    public function validatedBy(): string
    {
        return FormatEmailValidator::class;
    }

    /**
     * @return string
    */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
