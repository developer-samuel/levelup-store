<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Brand\Validator;

use Symfony\Component\Validator\Constraint;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Segment\Brand\Constraint\UniqueBrandName
};

use App\Core\Ports\Segment\Brand\BrandRepositoryContract;

final class UniqueBrandNameValidator extends AbstractConstraintValidator
{
    /**
     * @param BrandRepositoryContract $brandRepository
    */
    public function __construct(
        private readonly BrandRepositoryContract $brandRepository,
    ) {}

    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
    */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, UniqueBrandName::class);

        if (!$constraint instanceof UniqueBrandName) {
            return;
        }

        if (!$this->shouldValidate($value) || !is_string($value)) {
            return;
        }

        $name = $value;

        if ($this->brandRepository->existsByName($name)) {
            $this->addViolation($constraint->message, [
                '{{ value }}' => $name,
            ]);
        }
    }
}
