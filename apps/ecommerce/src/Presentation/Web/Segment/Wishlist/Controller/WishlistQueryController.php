<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Wishlist\Controller;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Wishlist\Service\WishlistQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Shared\Responder\HttpResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class WishlistQueryController extends AbstractQueryController
{
    public function __construct(
        private readonly WishlistQueryContract $wishlistQuery,
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
        $user = $this->securityProvider->getCurrentUser();

        if ($user === null) {
            return HttpResponder::unauthorized();
        }

        $records = $this->wishlistQuery->fetchAllForUser($user);

        return $this->render('features/wishlist/index.html.twig', [
            'records' => $records,
        ]);
    }
}
