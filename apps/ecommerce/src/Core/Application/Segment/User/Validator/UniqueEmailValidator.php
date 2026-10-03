<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Validator;

use Symfony\{
    Bundle\SecurityBundle\Security,
    Component\Validator\Constraint
};

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Segment\User\Constraint\UniqueEmail
};

use App\Core\Ports\Segment\User\Repository\UserRepositoryContract;

final class UniqueEmailValidator extends AbstractConstraintValidator
{
    public function __construct(
        private readonly Security $security,
        private readonly UserRepositoryContract $userRepository,
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, UniqueEmail::class);

        if (!$constraint instanceof UniqueEmail) {
            return;
        }

        if (!$this->shouldValidate($value)) {
            return;
        }

        if (!is_string($value)) {
            return;
        }

        $email = $value;

        if ($this->isCurrentUserEmail($email)) {
            return;
        }

        if ($this->emailExists($email)) {
            $this->addViolation($constraint->message, [
                '{{ value }}' => $email,
            ]);
        }
    }

    private function isCurrentUserEmail(string $email): bool
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof User) {
            return false;
        }

        return $currentUser->getUserIdentifier() === $email;
    }

    private function emailExists(string $email): bool
    {
        return $this->userRepository->findByEmail($email) !== null;
    }
}
