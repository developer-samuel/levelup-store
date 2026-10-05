<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Validator;

use Symfony\{
    Component\Validator\Constraint
};

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Shared\Constraint\TermsAcceptedConstraint
};

final class TermsAcceptedConstraintValidator extends AbstractConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, TermsAcceptedConstraint::class);

        if (!$constraint instanceof TermsAcceptedConstraint) {
            return;
        }

        if (!$this->shouldValidate($value)) {
            return;
        }

        if ($value !== true && $value !== 'on') {
            $this->addViolation($constraint->message);
        }
    }
}
