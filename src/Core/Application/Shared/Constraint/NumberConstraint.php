<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Constraint;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Shared\Validator\NumberConstraintValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class NumberConstraint extends Constraint
{
    public string $message;

    /**
     * @param string $label
    */
    public function __construct(string $label)
    {
        parent::__construct();

        $this->message = $label . ' must contain at least one number.';
    }

    /**
     * @return string
    */
    public function validatedBy(): string
    {
        return NumberConstraintValidator::class;
    }

    /**
     * @return string
    */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
