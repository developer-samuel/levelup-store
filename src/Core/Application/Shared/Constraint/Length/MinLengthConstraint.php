<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Constraint\Length;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Shared\Validator\Length\MinLengthConstraintValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class MinLengthConstraint extends Constraint
{
    public string $message;
    public int $min;

    /**
     * @param string $label
     * @param int $min
    */
    public function __construct(string $label, int $min)
    {
        parent::__construct();

        $this->min = $min;
        $this->message = $label . ' must be at least {{ limit }} characters long.';
    }

    /**
     * @return string
    */
    public function validatedBy(): string
    {
        return MinLengthConstraintValidator::class;
    }

    /**
     * @return string
    */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
