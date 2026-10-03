<?php

declare(strict_types=1);

namespace App\Core\Application\Abstract\Validator;

use Symfony\{
    Component\Validator\Constraint,
    Component\Validator\ConstraintValidator,
    Component\Validator\Exception\UnexpectedTypeException
};

abstract class AbstractConstraintValidator extends ConstraintValidator
{
    /** @param class-string<Constraint> $expectedConstraint */
    protected function assertConstraintType(mixed $constraint, string $expectedConstraint): void
    {
        if (!$constraint instanceof $expectedConstraint) {
            throw new UnexpectedTypeException($constraint, $expectedConstraint);
        }
    }

    protected function shouldValidate(mixed $value): bool
    {
        return !($value === null || $value === '');
    }

    /** @param array<string,string> $parameters */
    protected function addViolation(string $message, array $parameters = []): void
    {
        $builder = $this->context->buildViolation($message);

        foreach ($parameters as $k => $v) {
            $builder->setParameter($k, $v);
        }

        $builder->addViolation();
    }
}
