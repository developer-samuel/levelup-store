<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Validator;

use Symfony\Component\Validator\Constraint;

use App\Core\Application\{
    Abstract\Validator\AbstractConstraintValidator,
    Segment\Product\Constraint\UniqueProductVariantEanCode
};

use App\Core\Ports\Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract;

final class UniqueProductVariantEanCodeValidator extends AbstractConstraintValidator
{
    /**
     * @param ProductVariantEanRepositoryContract $variantRepository
    */
    public function __construct(
        private readonly ProductVariantEanRepositoryContract $variantRepository,
    ) {}

    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
    */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->assertConstraintType($constraint, UniqueProductVariantEanCode::class);

        if (!$constraint instanceof UniqueProductVariantEanCode) {
            return;
        }

        if (!$this->shouldValidate($value) || !is_string($value)) {
            return;
        }

        $ean = $value;

        if ($this->variantRepository->existsByCode($ean)) {
            $this->addViolation($constraint->message, [
                '{{ value }}' => $ean,
            ]);
        }
    }
}
