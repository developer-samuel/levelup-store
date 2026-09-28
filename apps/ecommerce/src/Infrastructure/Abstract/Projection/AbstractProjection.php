<?php

declare(strict_types=1);

namespace App\Infrastructure\Abstract\Projection;

abstract class AbstractProjection
{
    /** @var array<string, string> */
    protected const KEYWORD = ['type' => 'keyword'];

    /** @var array<string, string> */
    protected const FLOAT = ['type' => 'float'];

    /** @var array<string, string> */
    protected const BOOLEAN = ['type' => 'boolean'];

    /** @var array<string, string> */
    protected const INTEGER = ['type' => 'integer'];

    /** @var array<string, string> */
    protected const DATE = ['type' => 'date'];

    /** @var array<string, string> */
    protected const TEXT = ['type' => 'text'];
    
    /** @var array<string, mixed> */
    protected const TEXT_WITH_KEYWORD = ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]];

    /**
     * @return array<string, mixed>
    */
    abstract protected static function properties(): array;

    /**
     * @return array<string, mixed>
    */
    public static function mapping(): array
    {
        return [
            'settings' => [
                'number_of_shards'   => 1,
                'number_of_replicas' => 0,
            ],
            'mappings' => [
                'properties' => static::properties(),
            ],
        ];
    }
}
