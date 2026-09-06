<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Constraint;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Shared\Validator\SpecialCharacterConstraintValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class SpecialCharacterConstraint extends Constraint
{
    public string $message;

    /**
     * @param string $label
    */
    public function __construct(string $label)
    {
        parent::__construct();

        $this->message = $label . ' must contain at least one special character.';
    }

    /**
     * @return string
    */
    public function validatedBy(): string
    {
        return SpecialCharacterConstraintValidator::class;
    }

    /**
     * @return string
    */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
