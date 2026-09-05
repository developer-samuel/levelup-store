<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Validator\Length;

use Symfony\Component\Validator\Constraint;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Shared\Constraint\Length\AllMaxLengthConstraint
};

use App\Shared\Utils\Calculator\LengthCalculator;

final class AllMaxLengthConstraintValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
    */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, AllMaxLengthConstraint::class);

        if (!$constraint instanceof AllMaxLengthConstraint) {
            return;
        }

        if (!$this->shouldValidate($value)) {
            return;
        }

        $this->validateValues($this->normalizeValue($value), $constraint);
    }

    /**
     * @param mixed $value
     *
     * @return array<mixed>
    */
    private function normalizeValue(mixed $value): array
    {
        return is_array($value) ? $value : [$value];
    }

    /**
     * @param array<mixed> $values
     * @param AllMaxLengthConstraint $constraint
     *
     * @return void
    */
    private function validateValues(array $values, AllMaxLengthConstraint $constraint): void
    {
        foreach ($values as $v) {
            $length = LengthCalculator::getLength($v);

            if ($length !== null && $length > $constraint->max) {
                $this->addViolation(
                    $constraint->message,
                    ['{{ limit }}' => (string) $constraint->max],
                );
            }
        }
    }
}
