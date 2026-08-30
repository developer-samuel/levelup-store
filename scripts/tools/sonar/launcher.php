<?php

declare(strict_types=1);

require_once 'scripts/common/launcher.php';

$returnVar = launch(
    'scripts\\tools\\sonar\\batch\\run.cmd',
    'scripts/tools/sonar/bash/run.sh'
);
exit($returnVar);
