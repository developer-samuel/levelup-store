<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Subscriber;

use Symfony\{
    Component\EventDispatcher\EventSubscriberInterface,
    Component\HttpFoundation\Request,
    Component\HttpKernel\Event\ControllerEvent
};

use Twig\Environment;

use App\Core\Ports\{
    Segment\Product\Service\Query\ProductCacheQueryContract,
    Shared\Logging\AppLoggerContract
};

final readonly class ProductSubscriber implements EventSubscriberInterface
{
    /**
     * @param Environment $twig
     * @param AppLoggerContract $logger
     * @param ProductCacheQueryContract $productCacheQuery
    */
    public function __construct(
        private Environment $twig,
        private AppLoggerContract $logger,
        private ProductCacheQueryContract $productCacheQuery,
    ) {}

    /**
     * @return array<string, string>
    */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onKernelController',
        ];
    }

    /**
     * @param ControllerEvent $event
     *
     * @return void
    */
    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        try {
            $category = $this->getStringQueryParam($request, 'category');
            $type = $this->getStringQueryParam($request, 'type');

            $isDiscountRoute = $this->isDiscountRoute($event);

            $title = $this->productCacheQuery->getTitle($category, $type, $isDiscountRoute);
            $route = $this->productCacheQuery->getRoute($request->getPathInfo());

            $this->addGlobalVariablesToTwig($title, $route);
        } catch (\Throwable $throwable) {
            $title = '';
            $route = '';

            $this->logger->alert(
                'ProductSubscriber error while fetching values',
                $throwable,
            );
        }

        $this->addGlobalVariablesToTwig($title, $route);
    }

    /**
     * @param Request $request
     * @param string $key
     *
     * @return string|null
    */
    private function getStringQueryParam(Request $request, string $key): ?string
    {
        $value = $request->query->get($key);

        return is_string($value) ? $value : null;
    }

    /**
     * @param ControllerEvent $event
     *
     * @return bool
    */
    private function isDiscountRoute(ControllerEvent $event): bool
    {
        return str_contains($event->getRequest()->getPathInfo(), '/discounts');
    }

    /**
     * @param string $title
     * @param string $route
     *
     * @return void
    */
    private function addGlobalVariablesToTwig(string $title, string $route): void
    {
        $this->twig->addGlobal('productsTitle', $title);
        $this->twig->addGlobal('productRoute', $route);
    }
}
