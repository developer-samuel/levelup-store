<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Order\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Order\Handler\Query\GetOrderDetailQueryHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class AdminOrderQueryController extends AbstractQueryController
{
    public function __construct(
        private readonly GetOrderDetailQueryHandlerContract $getOrderDetailQueryHandler,
        SecurityProviderContract $securityProvider,
        ExceptionResponder $exceptionResponder,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $securityProvider,
            $exceptionResponder,
            $logger,
        );
    }

    public function index(): Response
    {
        return $this->renderPage('features/admin/views/order/main/index.html.twig');
    }

    public function show(string $code): Response
    {
        $result = $this->getOrderDetailQueryHandler->handle($code);
        if ($result === null) {
            return $this->redirectToRoute('admin_orders_index');
        }

        return $this->render(
            'features/admin/views/order/main/status.html.twig',
            $result->toArray(),
        );
    }
}
