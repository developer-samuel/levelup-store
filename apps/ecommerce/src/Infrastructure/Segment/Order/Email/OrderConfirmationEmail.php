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

use App\Core\Ports\Web\Segment\Order\Renderer\Email\OrderConfirmationEmailRendererContract;

use App\Infrastructure\Abstract\Email\AbstractEmail;

final class OrderConfirmationEmail extends AbstractEmail
{
    public function __construct(
        private readonly OrderConfirmationEmailRendererContract $renderer,
        MailerInterface $mailer,
        string $fromEmail,
    ) {
        parent::__construct($mailer, $fromEmail);
    }

    /** @param OrderItemEmailObject[] $items */
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
