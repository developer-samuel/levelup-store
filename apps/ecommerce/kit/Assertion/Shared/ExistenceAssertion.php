<?php

declare(strict_types=1);

namespace Kit\Assertion\Shared;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ExistenceAssertion
{
    /**
     * @param object|null $object
     * @param string $objectName
     *
     * @return void
     *
     * @throws NotFoundHttpException
    */
    public static function assertExists(?object $object, string $objectName): void
    {
        if ($object === null) {
            throw new NotFoundHttpException($objectName . ' not found');
        }
    }
}
