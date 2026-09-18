<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Validation;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class PasswordCheckFields
{
    /**
     * @param ExecutionContextInterface $context
     * @param mixed $value1
     * @param mixed $value2
     * @param string $field1
     * @param string $field2
     * @param string $message
     *
     * @return void
    */
    public static function validatePasswordsMatch(
        ExecutionContextInterface $context,
        mixed $value1,
        mixed $value2,
        string $field1,
        string $field2,
        string $message = 'Passwords do not match.',
    ): void {
        if ($value1 !== $value2) {
            self::addViolationForFields($context, $field1, $field2, $message);
        }
    }

    /**
     * @param ExecutionContextInterface $context
     * @param string $field1
     * @param string $field2
     * @param string $message
     *
     * @return void
    */
    private static function addViolationForFields(
        ExecutionContextInterface $context,
        string $field1,
        string $field2,
        string $message,
    ): void {
        $context->buildViolation($message)
            ->atPath($field1)
            ->addViolation();

        $context->buildViolation($message)
            ->atPath($field2)
            ->addViolation();
    }
}
