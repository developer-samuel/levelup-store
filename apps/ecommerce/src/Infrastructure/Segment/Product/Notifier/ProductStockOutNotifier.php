<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Event\ProductStockOutEvent
};

use App\Core\Ports\Segment\Product\Notifier\ProductStockOutNotifierContract;

final readonly class ProductStockOutNotifier implements ProductStockOutNotifierContract
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function notify(ProductVariantStock $stock): void
    {
        $this->dispatcher->dispatch(new ProductStockOutEvent($stock));
    }
}
