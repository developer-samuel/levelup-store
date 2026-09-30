<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Order\Controller\Query;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\HttpFoundation\Response
};

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Order\Handler\Command\OrderSuccessCleanupCommandHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder,
    Shared\Twig\CartStateClearer
};

final class OrderStatusQueryController extends AbstractQueryController
{
    /**
     * @param OrderSuccessCleanupCommandHandlerContract $orderSuccessCleanupCommandHandler
     * @param CartStateClearer $cartStateClearer
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly OrderSuccessCleanupCommandHandlerContract $orderSuccessCleanupCommandHandler,
        private readonly CartStateClearer $cartStateClearer,
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
     * @param Request $request
     *
     * @return Response
    */
    public function success(Request $request): Response
    {
        $user = $this->securityProvider->getCurrentUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        $sessionId = $request->query->get('session_id');

        $this->orderSuccessCleanupCommandHandler->handle($sessionId, $user);

        $this->cartStateClearer->clear();

        return $this->renderPage('features/order/status/success.html.twig');
    }

    /**
     * @return Response
    */
    public function cancel(): Response
    {
        return $this->renderPage('features/order/status/cancel.html.twig');
    }

    /**
     * @return Response
    */
    public function error(): Response
    {
        return $this->renderPage('features/order/status/error.html.twig');
    }
}
