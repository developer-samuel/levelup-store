<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Validator;

use Symfony\Component\Validator\Constraint;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Shared\Constraint\NotBlankConstraint
};

final class NotBlankConstraintValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
    */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, NotBlankConstraint::class);

        if (!$constraint instanceof NotBlankConstraint) {
            return;
        }

        if ($value === null || $value === '') {
            $this->addViolation($constraint->message);
        }
    }
}
