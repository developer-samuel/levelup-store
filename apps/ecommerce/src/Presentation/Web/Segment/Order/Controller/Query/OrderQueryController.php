<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Order\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Order\Handler\Query\GetOrderCreateQueryHandlerContract,
    Segment\Order\Handler\Query\GetOrderDetailQueryHandlerContract,
    Segment\Order\Handler\Query\GetOrderListQueryHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

use App\Shared\Responder\ErrorResponder;

final class OrderQueryController extends AbstractQueryController
{
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

    public function index(): Response
    {
        $orders = $this->getOrderListQueryHandler->handle();

        return $this->render('features/order/catalog/index.html.twig', [
            'orders' => $orders,
        ]);
    }

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
