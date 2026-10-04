<?php

declare(strict_types=1);

namespace App\Presentation\Api\Cookie;

use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\Core\Application\Cookie\CookieFactory;

use App\Core\Ports\{
    Gateways\Internal\Cookie\CookieGatewayContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCommandController,
    Shared\Responder\HttpResponder
};

final class CookieApiCommandController extends AbstractCommandController
{
    public function __construct(
        private readonly CookieGatewayContract $cookieGateway,
        private readonly CookieFactory $cookieFactory,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    #[OA\Post(
        path: '/api/cookies/store',
        summary: 'Save cookie consent preferences',
        tags: ['Cookies'],
        security: [],
        responses: [
            new OA\Response(response: 200, description: 'Cookie preferences saved'),
        ],
    )]
    public function store(): JsonResponse
    {
        return $this->handleCommand(function () {
            $cookieData = $this->cookieFactory->fromObject();

            $cookie = $this->cookieGateway->apply($cookieData);

            $response = HttpResponder::success([], 'Cookie preferences saved.');
            $response->headers->setCookie($cookie);

            return $response;
        });
    }
}
