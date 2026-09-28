<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Order\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Order\Handler\Query\GetOrderDetailQueryHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminOrderQueryController extends AbstractQueryController
{
    /**
     * @param GetOrderDetailQueryHandlerContract $getOrderDetailQueryHandler
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
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

    /**
     * @return Response
    */
    public function index(): Response
    {
        return $this->renderPage('features/admin/views/order/main/index.html.twig');
    }

    /**
     * @param string $code
     *
     * @return Response
    */
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
