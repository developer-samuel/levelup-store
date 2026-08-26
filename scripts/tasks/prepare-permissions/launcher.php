<?php

declare(strict_types=1);

require_once 'scripts/common/launcher.php';

$returnVar = launch(
    'scripts\\tasks\\permissions\\batch\\run.cmd',
    'scripts/tasks/permissions/bash/run.sh'
);
exit($returnVar);
