<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Order\Controller\Query;

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
    Shared\Responder\ExceptionResponder,
    Shared\Twig\CartStateClearer,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class OrderStatusQueryController extends AbstractQueryController
{
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

    public function cancel(): Response
    {
        return $this->renderPage('features/order/status/cancel.html.twig');
    }

    public function error(): Response
    {
        return $this->renderPage('features/order/status/error.html.twig');
    }
}
