<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Validator;

use Symfony\Component\Validator\Constraint;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Shared\Constraint\SpecialCharacterConstraint
};

final class SpecialCharacterConstraintValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
    */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, SpecialCharacterConstraint::class);

        if (!$constraint instanceof SpecialCharacterConstraint) {
            return;
        }

        if (!$this->shouldValidate($value)) {
            return;
        }

        if (!is_string($value) || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
            $this->addViolation($constraint->message);
        }
    }
}
