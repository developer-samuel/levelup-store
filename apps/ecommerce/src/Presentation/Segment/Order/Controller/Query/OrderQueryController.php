<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Order\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Order\Handler\Query\GetOrderCreateQueryHandlerContract,
    Segment\Order\Handler\Query\GetOrderDetailQueryHandlerContract,
    Segment\Order\Handler\Query\GetOrderListQueryHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

use App\Shared\Responder\ErrorResponder;

final class OrderQueryController extends AbstractQueryController
{
    /**
     * @param GetOrderListQueryHandlerContract $getOrderListQueryHandler
     * @param GetOrderDetailQueryHandlerContract $getOrderDetailQueryHandler
     * @param GetOrderCreateQueryHandlerContract $getOrderCreateQueryHandler
     * @param ErrorResponder $errorResponder
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly GetOrderListQueryHandlerContract $getOrderListQueryHandler,
        private readonly GetOrderDetailQueryHandlerContract $getOrderDetailQueryHandler,
        private readonly GetOrderCreateQueryHandlerContract $getOrderCreateQueryHandler,
        private readonly ErrorResponder $errorResponder,
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
        $orders = $this->getOrderListQueryHandler->handle();

        return $this->render('features/order/catalog/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @param string $code
     *
     * @return Response
    */
    public function show(string $code): Response
    {
        $user = $this->securityProvider->getCurrentUser();
        if ($user === null) {
            return $this->errorResponder->renderUnauthorized();
        }

        $result = $this->getOrderDetailQueryHandler->handle($code, $user);
        if ($result === null) {
            return $this->redirectToRoute('home');
        }

        return $this->render(
            'features/order/detail/show.html.twig',
            $result->toArray(),
        );
    }

    /**
     * @return Response
    */
    public function create(): Response
    {
        $data = $this->getOrderCreateQueryHandler->handle();

        if ($data->cartEmpty) {
            return $this->redirectToRoute('home');
        }

        return $this->render('features/order/create/create.html.twig', [
            'order' => $data,
        ]);
    }
}
