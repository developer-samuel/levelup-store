<?php

declare(strict_types=1);

namespace App\Presentation\Home;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Home\HomeCacheQueryContract,
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

class HomeQueryController extends AbstractQueryController
{
    /**
     * @param HomeCacheQueryContract $homeCacheQuery
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly HomeCacheQueryContract $homeCacheQuery,
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
        $data = $this->homeCacheQuery->getHomeData();

        return $this->render('features/home/index.html.twig', [
            'products' => $data['products'],
            'banners'  => $data['banners'],
        ]);
    }
}
