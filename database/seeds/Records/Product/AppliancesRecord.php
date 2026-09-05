<?php

declare(strict_types=1);

namespace Database\Seeds\Records\Product;

use Database\{
    Seeds\Abstract\AbstractDataRecord,
    Seeds\Utils\Resolver\PathResolver
};

final class AppliancesRecord extends AbstractDataRecord
{
    private const FOLDER = __DIR__ . '/../../../data/products/records/appliances/';
    private const FILES = __DIR__ . '/../../../data/products/files/appliances.json';

    /**
     * @return string[]
    */
    protected function getFilePaths(): array
    {
        return PathResolver::fromJson(self::FOLDER, self::FILES);
    }
}
