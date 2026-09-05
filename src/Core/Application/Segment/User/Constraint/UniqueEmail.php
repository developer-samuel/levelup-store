<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Constraint;

use Symfony\Component\Validator\Constraint;

use Attribute;

use App\Core\Application\Segment\User\Validator\UniqueEmailValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class UniqueEmail extends Constraint
{
    public string $message = 'Email "{{ value }}" is already taken.';

    public ?int $ignoreUserId = null;

    /**
     * @param int|null $ignoreUserId
    */
    public function __construct(
        ?int $ignoreUserId = null,
    ) {
        parent::__construct();

        $this->ignoreUserId = $ignoreUserId;
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
        return UniqueEmailValidator::class;
    }
}
