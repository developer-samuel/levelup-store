<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Constraint;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Admin\Segment\Product\Validator\EanConstraintValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class EanConstraint extends Constraint
{
    public string $messageLength = 'EAN must be exactly {{ limit }} digits.';
    public string $messageNumber = 'EAN must contain only numbers (0-9).';

    public int $length;

    /**
     * @param int $length
    */
    public function __construct(int $length)
    {
        parent::__construct();

        $this->length = $length;
    }

    /**
     * @return string
    */
    public function validatedBy(): string
    {
        return EanConstraintValidator::class;
    }

    /**
     * @return string
    */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
