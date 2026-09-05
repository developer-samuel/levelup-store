<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Validator;

use Symfony\{
    Component\Validator\Constraint,
    Component\Validator\Exception\UnexpectedTypeException
};

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Shared\Constraint\TermsAcceptedConstraint
};

final class TermsAcceptedConstraintValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
     *
     * @throws UnexpectedTypeException
     */
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
