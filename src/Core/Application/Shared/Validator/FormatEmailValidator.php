<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Validator;

use Symfony\Component\Validator\Constraint;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Shared\Constraint\EmailFormat,
};

final class FormatEmailValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
    */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, EmailFormat::class);

        if (!$constraint instanceof EmailFormat) {
            return;
        }

        if (!$this->shouldValidate($value)) {
            return;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addViolation($constraint->message);
        }
    }
}
