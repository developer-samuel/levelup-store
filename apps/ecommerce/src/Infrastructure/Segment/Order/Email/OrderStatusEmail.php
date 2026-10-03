<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Email;

use Symfony\{
    Component\Mailer\MailerInterface,
    Component\Routing\Generator\UrlGeneratorInterface
};

use App\Core\Domain\Segment\Order\Entity\Order;

use App\Core\Ports\Web\Segment\Order\Renderer\Email\OrderStatusEmailRendererContract;

use App\Infrastructure\Abstract\Email\AbstractEmail;

final class OrderStatusEmail extends AbstractEmail
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly OrderStatusEmailRendererContract $renderer,
        MailerInterface $mailer,
        string $fromEmail,
    ) {
        parent::__construct($mailer, $fromEmail);
    }

    public function send(string $toEmail, Order $order): void
    {
        $email = $this->createBaseEmail(
            $toEmail,
            'Order Status Update',
        )
        ->html($this->renderer->renderOrderStatusEmail($order, $this->buildOrderUrl($order)));

        $this->sendEmail($email);
    }

    private function buildOrderUrl(Order $order): string
    {
        return $this->urlGenerator->generate(
            'orders_show',
            ['code' => $order->getCode()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
