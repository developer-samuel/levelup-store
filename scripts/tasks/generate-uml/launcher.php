<?php

declare(strict_types=1);

require_once 'scripts/common/launcher.php';

$returnVar = launch(
    'scripts\\tasks\\generate-uml\\batch\\run.cmd',
    'scripts/tasks/generate-uml/bash/run.sh'
);
exit($returnVar);
