<?php

declare(strict_types=1);

namespace Tests\Support\Provides;

trait AssertsPersisted
{
    /** @param object[] $persisted */
    private function assertPersistedContains(array $persisted, string $class): void
    {
        self::assertNotEmpty(array_filter($persisted, fn($e) => $e instanceof $class));
    }
}
