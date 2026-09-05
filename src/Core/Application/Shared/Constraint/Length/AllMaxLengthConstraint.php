<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Constraint\Length;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Shared\Validator\Length\AllMaxLengthConstraintValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class AllMaxLengthConstraint extends Constraint
{
    public string $message;
    public int $max;

    /**
     * @param string $label
     * @param int $max
    */
    public function __construct(string $label, int $max)
    {
        parent::__construct();

        $this->max = $max;
        $this->message = $label . ' cannot be longer than {{ limit }} characters.';
    }

    /**
     * @return string
    */
    public function validatedBy(): string
    {
        return AllMaxLengthConstraintValidator::class;
    }

    /**
     * @return string
    */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
