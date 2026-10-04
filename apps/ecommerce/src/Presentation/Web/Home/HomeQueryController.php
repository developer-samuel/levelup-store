<?php

declare(strict_types=1);

namespace App\Presentation\Web\Home;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Home\HomeCacheQueryContract,
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class HomeQueryController extends AbstractQueryController
{
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
    
    public function index(): Response
    {
        $data = $this->homeCacheQuery->getHomeData();

        return $this->render('features/home/index.html.twig', [
            'products'   => $data['products'],
            'categories' => $data['categories'] ?? [],
            'banners'    => $data['banners'],
        ]);
    }
}
