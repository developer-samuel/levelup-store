<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Subtype;

use Packages\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Subtype\Entity\Subtype;

final class SubtypeAssertion
{
    /**
     * @param Subtype|null $subtype
     *
     * @return void
     *
     * @throws \RuntimeException
     *
     * @phpstan-assert Subtype $subtype
    */
    public static function assertExists(?Subtype $subtype): void
    {
        ExistenceAssertion::assertExists($subtype, 'Subtype');
    }
}
