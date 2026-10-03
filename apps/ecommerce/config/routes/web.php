<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->import(__DIR__ . '/web/app/setup.php');
    $routes->import(__DIR__ . '/web/admin/setup.php');
};
