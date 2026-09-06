<?php

declare(strict_types=1);

function launch(string $unixCmd): never
{
    if (PHP_OS_FAMILY === 'Windows' && getenv('SHELL') === false) {
        echo "Error: Windows CMD/PowerShell is not supported. Use WSL to run this script.\n";
        exit(1);
    }

    passthru("bash $unixCmd", $returnVar);

    exit($returnVar);
}
