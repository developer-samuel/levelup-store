<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Brand\Constraint;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Segment\Brand\Validator\UniqueBrandNameValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class UniqueBrandName extends Constraint
{
    public string $message = 'This brand name is already taken.';

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
        return UniqueBrandNameValidator::class;
    }
}
