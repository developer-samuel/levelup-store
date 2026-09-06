<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Validator;

use Symfony\Component\Validator\Constraint;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Shared\Constraint\NumberConstraint
};

final class NumberConstraintValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
    */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, NumberConstraint::class);

        if (!$constraint instanceof NumberConstraint) {
            return;
        }

        if (!$this->shouldValidate($value)) {
            return;
        }

        if (!is_string($value) || !preg_match('/\d/', $value)) {
            $this->addViolation($constraint->message);
        }
    }
}
