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
    /**
     * @param mixed $constraint
     * @param class-string<Constraint> $expectedConstraint
     *
     * @throws UnexpectedTypeException
    */
    protected function assertConstraintType(mixed $constraint, string $expectedConstraint): void
    {
        if (!$constraint instanceof $expectedConstraint) {
            throw new UnexpectedTypeException($constraint, $expectedConstraint);
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
    */
    protected function shouldValidate(mixed $value): bool
    {
        return !($value === null || $value === '');
    }

    /**
     * @param string $message
     * @param array<string,string> $parameters
     *
     * @return void
    */
    protected function addViolation(string $message, array $parameters = []): void
    {
        $builder = $this->context->buildViolation($message);

        foreach ($parameters as $k => $v) {
            $builder->setParameter($k, $v);
        }

        $builder->addViolation();
    }
}
