<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Order\Renderer\Email;

use Twig\Environment;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\ValueObject\Email\OrderStatusEmailObject
};

use App\Core\Ports\Segment\Order\Renderer\Email\OrderStatusEmailRendererContract;

final readonly class OrderStatusEmailRenderer implements OrderStatusEmailRendererContract
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @param Order $order
     * @param string $url
     *
     * @return string
    */
    public function renderOrderStatusEmail(Order $order, string $url): string
    {
        $data = new OrderStatusEmailObject($order, $url);

        return $this->twig->render(
            'emails/order/order-status.html.twig',
            $data->toArray(),
        );
    }
}
