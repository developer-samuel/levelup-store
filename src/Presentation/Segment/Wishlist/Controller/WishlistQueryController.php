<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Wishlist\Controller;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Wishlist\Service\WishlistQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder,
    Shared\Responder\HttpResponder
};

final class WishlistQueryController extends AbstractQueryController
{
    /**
     * @param WishlistQueryContract $wishlistQuery
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
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

    /**
     * @return Response
    */
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
