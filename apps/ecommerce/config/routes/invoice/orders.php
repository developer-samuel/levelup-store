<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Segment\Order\Controller\Command\OrderInvoiceCommandController;

return function (RoutingConfigurator $routes) {
    // Route for download order invoice
    $routes->add('orders_invoice_download', '/orders/{code}/invoice/download')
        ->controller([OrderInvoiceCommandController::class, 'store'])
        ->methods(['GET']);
};
