<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Order\Controller\Command;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Segment\Order\Handler\Command\GenerateOrderInvoiceHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\Abstract\Controller\Command\AbstractCommandController;

final class OrderInvoiceCommandController extends AbstractCommandController
{
    /**
     * @param GenerateOrderInvoiceHandlerContract $generateOrderInvoiceHandler
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly GenerateOrderInvoiceHandlerContract $generateOrderInvoiceHandler,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param string $code
     *
     * @return Response
    */
    public function store(string $code): Response
    {
        $pdfContent = $this->generateOrderInvoiceHandler->handle($code);

        return $this->createPdfResponse($pdfContent);
    }

    /**
     * @param string $pdfContent
     *
     * @return Response
    */
    private function createPdfResponse(string $pdfContent): Response
    {
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="invoice.pdf"',
            ],
        );
    }
}
