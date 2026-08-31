<?php

declare(strict_types=1);

namespace App\Presentation\Cookie\Controller\Command;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Core\Application\Cookie\Factory\CookieFactory;

use App\Core\Ports\{
    Gateways\Internal\Cookie\CookieGatewayContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCommandController,
    Shared\Responder\HttpResponder
};

class CookieApiCommandController extends AbstractCommandController
{
    /**
     * @param CookieGatewayContract $cookieGateway
     * @param CookieFactory $cookieFactory
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly CookieGatewayContract $cookieGateway,
        private readonly CookieFactory $cookieFactory,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @return JsonResponse
    */
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
