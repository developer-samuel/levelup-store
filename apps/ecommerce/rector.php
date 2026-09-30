<?php

declare(strict_types=1);

use Rector\{
    Config\RectorConfig,
    ValueObject\PhpVersion
};

use Tools\Rector\Rules\FinalizeNonAbstractClassRector;

$sets = require __DIR__ . '/tools/rector/sets.php';
$paths = require __DIR__ . '/tools/rector/paths.php';
$skip = require __DIR__ . '/tools/rector/skip.php';

return static function (RectorConfig $rectorConfig) use ($sets, $paths, $skip): void {
    $rectorConfig->phpVersion(PhpVersion::PHP_82);

    $rectorConfig->sets($sets);
    $rectorConfig->paths($paths);
    $rectorConfig->skip($skip);

    $rectorConfig->rules([
        FinalizeNonAbstractClassRector::class,
    ]);
};
