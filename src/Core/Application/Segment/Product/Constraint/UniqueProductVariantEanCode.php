<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Constraint;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Segment\Product\Validator\UniqueProductVariantEanCodeValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class UniqueProductVariantEanCode extends Constraint
{
    public string $message = 'This EAN code is already taken.';

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return string
    */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /**
     * @return string
    */
    public function validatedBy(): string
    {
        return UniqueProductVariantEanCodeValidator::class;
    }
}
