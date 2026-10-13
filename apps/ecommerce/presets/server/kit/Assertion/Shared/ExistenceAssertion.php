<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Shared;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ExistenceAssertion
{
    public static function assertExists(?object $object, string $objectName): void
    {
        if ($object === null) {
            throw new NotFoundHttpException($objectName . ' not found');
        }
    }
}
