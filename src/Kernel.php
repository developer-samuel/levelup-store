<?php

declare(strict_types=1);

namespace App;

use Symfony\{
    Bundle\FrameworkBundle\Kernel\MicroKernelTrait,
    Component\HttpKernel\Kernel as BaseKernel,
    Component\Routing\Loader\Configurator\RoutingConfigurator
};

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @param RoutingConfigurator $routes
     *
     * @return void
    */
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir() . '/config/routes/web.php');
    }
}
