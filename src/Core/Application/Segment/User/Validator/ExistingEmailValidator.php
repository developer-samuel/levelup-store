<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Validator;

use Symfony\Component\Validator\Constraint;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Segment\User\Constraint\ExistingEmail
};

use App\Core\Ports\Segment\User\Repository\UserRepositoryContract;

final class ExistingEmailValidator extends AbstractConstraintValidator
{
    /**
     * @param UserRepositoryContract $userRepository
    */
    public function __construct(
        private readonly UserRepositoryContract $userRepository,
    ) {}

    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, ExistingEmail::class);

        if (!$constraint instanceof ExistingEmail) {
            return;
        }

        if (!$this->shouldValidate($value) || !is_string($value)) {
            return;
        }

        $email = $value;

        if ($this->userRepository->findByEmail($email) === null) {
            $this->addViolation($constraint->message, [
                '{{ value }}' => $email,
            ]);
        }
    }
}
