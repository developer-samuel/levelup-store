<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\Product\Event\ProductStockOutEvent
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: ProductStockOutEvent::class)]
final readonly class AuditProductStockOutEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(ProductStockOutEvent $event): void
    {
        $this->audit->log(
            AuditAction::PRODUCT_STOCK_OUT,
            'ProductVariantStock',
            $event->stock->getId(),
            ['variant_id' => $event->stock->getVariant()->getId()],
        );
    }
}
