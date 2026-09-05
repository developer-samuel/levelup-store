<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Validator\Length;

use Symfony\Component\Validator\Constraint;

use Kit\Utils\Shared\DataSanitizer;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Shared\Constraint\Length\MinLengthConstraint
};

use App\Shared\Utils\Calculator\LengthCalculator;

class MinLengthConstraintValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
    */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, MinLengthConstraint::class);

        if (!$constraint instanceof MinLengthConstraint) {
            return;
        }

        if (!$this->shouldValidate($value)) {
            return;
        }

        $length = LengthCalculator::getLength(
            DataSanitizer::sanitizeString($value),
        );

        if ($length === null) {
            return;
        }

        if ($length < $constraint->min) {
            $this->addViolation(
                $constraint->message,
                ['{{ limit }}' => (string) $constraint->min],
            );
        }
    }
}
