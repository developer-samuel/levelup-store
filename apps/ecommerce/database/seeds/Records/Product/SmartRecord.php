<?php

declare(strict_types=1);

namespace Database\Seeds\Records\Product;

use Database\{
    Seeds\Abstract\AbstractDataRecord,
    Seeds\Records\Contracts\ProductRecordContract,
    Seeds\Utils\Resolver\PathResolver
};

final class SmartRecord extends AbstractDataRecord implements ProductRecordContract
{
    private const FOLDER = __DIR__ . '/../../../data/products/records/smart/';
    private const FILES = __DIR__ . '/../../../data/products/files/smart.json';

    /**
     * @return string[]
    */
    protected function getFilePaths(): array
    {
        return PathResolver::fromJson(self::FOLDER, self::FILES);
    }
}
