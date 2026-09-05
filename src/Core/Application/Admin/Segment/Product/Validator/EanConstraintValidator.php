<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Validator;

use Symfony\Component\Validator\Constraint;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Admin\Segment\Product\Constraint\EanConstraint
};

final class EanConstraintValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
    */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, EanConstraint::class);

        if (!$constraint instanceof EanConstraint) {
            return;
        }

        if (!is_string($value) && !is_int($value)) {
            return;
        }

        $value = (string) $value;

        if (!preg_match('/^\d+$/', $value)) {
            $this->addViolation($constraint->messageNumber);

            return;
        }

        if (strlen($value) !== $constraint->length) {
            $this->addViolation(
                $constraint->messageLength,
                ['{{ limit }}' => (string) $constraint->length],
            );
        }
    }
}
