<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Order\Renderer\Email;

use Twig\Environment;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling,
    Segment\Order\Entity\OrderPersonal,
    Segment\Order\Entity\OrderShipping,
    Segment\Order\ValueObject\Email\OrderConfirmationEmailObject,
    Segment\Order\ValueObject\Email\OrderItemEmailObject
};

use App\Core\Ports\Segment\Order\Renderer\Email\OrderConfirmationEmailRendererContract;

final readonly class OrderConfirmationEmailRenderer implements OrderConfirmationEmailRendererContract
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @param Order $order
     * @param OrderPersonal $personal
     * @param OrderBilling $billing
     * @param OrderShipping|null $shipping
     * @param OrderItemEmailObject[] $items
     *
     * @return string
    */
    public function renderOrderConfirmationEmail(
        Order $order,
        OrderPersonal $personal,
        OrderBilling $billing,
        ?OrderShipping $shipping,
        array $items,
    ): string {
        $data = $this->createEmailData($order, $personal, $billing, $shipping, $items);

        return $this->twig->render(
            'emails/order/order-confirmation.html.twig',
            $data->toArray(),
        );
    }

    /**
     * @param Order $order
     * @param OrderPersonal $personal
     * @param OrderBilling $billing
     * @param OrderShipping|null $shipping
     * @param OrderItemEmailObject[] $items
     *
     * @return OrderConfirmationEmailObject
    */
    private function createEmailData(
        Order $order,
        OrderPersonal $personal,
        OrderBilling $billing,
        ?OrderShipping $shipping,
        array $items,
    ): OrderConfirmationEmailObject {
        return new OrderConfirmationEmailObject(
            order: $order,
            personal: $personal,
            billing: $billing,
            shipping: $shipping,
            items: $items,
        );
    }
}
