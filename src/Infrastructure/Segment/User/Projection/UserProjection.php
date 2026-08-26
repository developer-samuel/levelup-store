<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\Projection;

final class UserProjection
{
    public const NAME = 'users';

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
                'properties' => [
                    'email'      => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                    'first_name' => ['type' => 'text'],
                    'last_name'  => ['type' => 'text'],
                    'role'       => ['type' => 'keyword'],
                    'created_at' => ['type' => 'date'],
                ],
            ],
        ];
    }
}
