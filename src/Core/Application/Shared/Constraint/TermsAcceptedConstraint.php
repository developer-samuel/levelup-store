<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Constraint;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Shared\Validator\TermsAcceptedConstraintValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class TermsAcceptedConstraint extends Constraint
{
    public string $message;

    /**
     * @param string $message
    */
    public function __construct(
        string $message = 'You must accept the terms and conditions.',
    ) {
        parent::__construct();
        $this->message = $message;
    }

    /**
     * @return string
    */
    public function validatedBy(): string
    {
        return TermsAcceptedConstraintValidator::class;
    }

    /**
     * @return string
    */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
