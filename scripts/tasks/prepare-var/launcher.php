<?php

declare(strict_types=1);

require_once 'scripts/common/launcher.php';

$returnVar = launch(
    'scripts\\tasks\\prepare-var\\batch\\run.cmd',
    'scripts/tasks/prepare-var/bash/run.sh'
);
exit($returnVar);
