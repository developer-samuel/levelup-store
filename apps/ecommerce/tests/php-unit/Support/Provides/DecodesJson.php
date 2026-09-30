<?php

declare(strict_types=1);

namespace Tests\Support\Provides;

trait DecodesJson
{
    /** @return array<string, mixed> */
    private function decodeJson(): array
    {
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $this->client->getResponse()->getContent(), true);

        return $data;
    }
}
