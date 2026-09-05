<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Constraint;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Segment\User\Validator\ExistingEmailValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ExistingEmail extends Constraint
{
    public string $message = 'No user exists with the provided email.';

    /**
     * @return string
    */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /**
     * @return string
    */
    public function validatedBy(): string
    {
        return ExistingEmailValidator::class;
    }
}
