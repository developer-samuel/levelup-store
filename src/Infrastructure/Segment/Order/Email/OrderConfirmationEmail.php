<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Email;

use Symfony\Component\Mailer\MailerInterface;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling,
    Segment\Order\Entity\OrderPersonal,
    Segment\Order\Entity\OrderShipping,
    Segment\Order\ValueObject\Email\OrderItemEmailObject
};

use App\Core\Ports\Segment\Order\Renderer\Email\OrderConfirmationEmailRendererContract;

use App\Infrastructure\Abstract\Email\AbstractEmail;

final class OrderConfirmationEmail extends AbstractEmail
{
    /**
     * @param OrderConfirmationEmailRendererContract $renderer
     * @param MailerInterface $mailer
     * @param string $fromEmail
    */
    public function __construct(
        private readonly OrderConfirmationEmailRendererContract $renderer,
        MailerInterface $mailer,
        string $fromEmail,
    ) {
        parent::__construct($mailer, $fromEmail);
    }

    /**
     * @param string $toEmail
     * @param Order $order
     * @param OrderPersonal $personal
     * @param OrderBilling $billing
     * @param OrderShipping|null $shipping
     * @param OrderItemEmailObject[] $items
     *
     * @return void
    */
    public function send(
        string $toEmail,
        Order $order,
        OrderPersonal $personal,
        OrderBilling $billing,
        ?OrderShipping $shipping,
        array $items,
    ): void {
        $emailHtml = $this->renderer->renderOrderConfirmationEmail(
            $order,
            $personal,
            $billing,
            $shipping,
            $items,
        );

        $email = $this->createBaseEmail(
            $toEmail,
            'Order Confirmation',
        )
        ->html($emailHtml);

        $this->sendEmail($email);
    }
}
