<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Projection;

use App\Infrastructure\Abstract\Projection\AbstractProjection;

final class ProductVariantProjection extends AbstractProjection
{
    public const NAME = 'product_variants';

    /** @return array<string, mixed> */
    protected static function properties(): array
    {
        $textWithSynonyms = [
            'type'     => 'text',
            'analyzer' => 'synonym_analyzer',
            'fields'   => ['keyword' => ['type' => 'keyword']],
        ];

        return [
            'name'            => $textWithSynonyms,
            'effective_price' => self::FLOAT,
            'has_discount'    => self::BOOLEAN,
            'is_available'    => self::BOOLEAN,
            'brand'           => self::TEXT_WITH_KEYWORD,
            'category'        => self::TEXT_WITH_KEYWORD,
            'type'            => $textWithSynonyms,
            'subtypes'        => $textWithSynonyms,
            'avg_rating'      => self::FLOAT,
            'created_at'      => self::DATE,
        ];
    }

    /**
     * @param string[] $synonyms
     *
     * @return array<string, mixed>
    */
    public static function mapping(array $synonyms = []): array
    {
        return [
            'settings' => [
                'number_of_shards'   => 1,
                'number_of_replicas' => 0,
                'analysis'           => [
                    'filter'   => [
                        'product_synonyms' => [
                            'type'     => 'synonym',
                            'synonyms' => $synonyms,
                        ],
                    ],
                    'analyzer' => [
                        'synonym_analyzer' => [
                            'type'      => 'custom',
                            'tokenizer' => 'standard',
                            'filter'    => ['lowercase', 'product_synonyms'],
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => static::properties(),
            ],
        ];
    }
}
