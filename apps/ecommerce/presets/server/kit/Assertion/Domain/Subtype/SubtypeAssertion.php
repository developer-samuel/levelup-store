<?php

declare(strict_types=1);

namespace Presets\Kit\Assertion\Domain\Subtype;

use Presets\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Subtype\Entity\Subtype;

final class SubtypeAssertion
{
    /** @phpstan-assert Subtype $subtype */
    public static function assertExists(?Subtype $subtype): void
    {
        ExistenceAssertion::assertExists($subtype, 'Subtype');
    }
}
