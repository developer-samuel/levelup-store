<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Handler\Command;

use Kit\{
    Assertion\Domain\Order\OrderAssertion,
    Assertion\Domain\User\UserAssertion
};

use App\Core\Ports\{
    Gateways\Internal\Order\OrderInvoiceGatewayContract,
    Security\SecurityPolicyContract,
    Segment\Order\Handler\Command\GenerateOrderInvoiceHandlerContract,
    Segment\Order\Service\Query\OrderInvoiceQueryContract,
    Shared\FileSystem\TempFileManagerContract,
    Shared\Logging\AppLoggerContract
};

final readonly class GenerateOrderInvoiceHandler implements GenerateOrderInvoiceHandlerContract
{
    public function __construct(
        private SecurityPolicyContract $securityPolicy,
        private OrderInvoiceGatewayContract $orderInvoiceAdapter,
        private OrderInvoiceQueryContract $orderInvoiceQuery,
        private TempFileManagerContract $tempFileManager,
        private AppLoggerContract $logger,
    ) {}

    /**
     * @param string $code
     *
     * @return string
    */
    public function handle(string $code): string
    {
        $user = UserAssertion::assertInstance(
            $this->securityPolicy->checkIfEmailVerified(),
        );

        try {
            OrderAssertion::assertOrderCode($code);

            $data = $this->orderInvoiceQuery->getInvoiceDetails($code);
            $pdfContent = $this->orderInvoiceAdapter->generate($data);

            if ($pdfContent === '') {
                throw new \Exception('Failed to read generated PDF file.');
            }

            $tmpFile = $this->tempFileManager->create($pdfContent, 'invoice_', '.pdf');
            $content = $this->tempFileManager->read($tmpFile);
            $this->tempFileManager->delete($tmpFile);

            return $content;
        } catch (\Throwable $throwable) {
            $this->logger->error("Failed to generate invoice", $throwable, $user, [
                'code' => $code,
            ]);

            throw $throwable;
        }
    }
}
