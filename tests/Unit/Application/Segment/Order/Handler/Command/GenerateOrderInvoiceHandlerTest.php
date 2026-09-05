<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Handler\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Shared\Exception\AccessDeniedException,
    Segment\User\Entity\User
};

use App\Core\Application\Segment\Order\Handler\Command\GenerateOrderInvoiceHandler;

use App\Core\Ports\{
    Gateways\Internal\Order\OrderInvoiceGatewayContract,
    Security\SecurityPolicyContract,
    Segment\Order\Handler\Command\GenerateOrderInvoiceHandlerContract,
    Segment\Order\Service\Query\OrderInvoiceQueryContract,
    Shared\FileSystem\TempFileManagerContract,
    Shared\Logging\AppLoggerContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Handler\Command\GenerateOrderInvoiceHandler
*/
final class GenerateOrderInvoiceHandlerTest extends TestCase
{
    private SecurityPolicyContract&MockObject $securityPolicy;
    private OrderInvoiceGatewayContract&MockObject $orderInvoiceAdapter;
    private OrderInvoiceQueryContract&MockObject $orderInvoiceQuery;
    private TempFileManagerContract&MockObject $tempFileManager;
    private AppLoggerContract&MockObject $logger;
    private GenerateOrderInvoiceHandler $handler;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initHandler();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(GenerateOrderInvoiceHandlerContract::class, $this->handler);
    }

    public function testHandleReturnsPdfContent(): void
    {
        $this->setupInvoiceGeneration(pdfContent: '%PDF-1.4 content');

        $result = $this->handler->handle('ORDER-001');

        $this->assertSame('%PDF-1.4 content', $result);
    }

    public function testHandleCallsInvoiceQueryWithCode(): void
    {
        $this->setupInvoiceGeneration();

        $this->orderInvoiceQuery
            ->expects($this->once())
            ->method('getInvoiceDetails')
            ->with('ORDER-001');

        $this->handler->handle('ORDER-001');
    }

    public function testHandleCallsGenerateWithInvoiceData(): void
    {
        $this->setupVerifiedUser();

        $invoiceData = ['order_id' => 1, 'total' => 99.99];
        $this->orderInvoiceQuery->method('getInvoiceDetails')->willReturn($invoiceData);

        $this->orderInvoiceAdapter
            ->expects($this->once())
            ->method('generate')
            ->with($invoiceData)
            ->willReturn('%PDF content');

        $this->tempFileManager->method('create')->willReturn('/tmp/invoice_abc.pdf');
        $this->tempFileManager->method('read')->willReturn('%PDF content');

        $this->handler->handle('ORDER-001');
    }

    public function testHandleCreatesTempFileWithPdfExtension(): void
    {
        $this->setupInvoiceGeneration(pdfContent: '%PDF content');

        $this->tempFileManager
            ->expects($this->once())
            ->method('create')
            ->with('%PDF content', 'invoice_', '.pdf')
            ->willReturn('/tmp/invoice_abc.pdf');

        $this->handler->handle('ORDER-001');
    }

    public function testHandleReadsTempFile(): void
    {
        $this->setupInvoiceGeneration(pdfContent: '%PDF content');

        $this->tempFileManager
            ->expects($this->once())
            ->method('read')
            ->with('/tmp/invoice_abc.pdf');

        $this->handler->handle('ORDER-001');
    }

    public function testHandleDeletesTempFileAfterRead(): void
    {
        $this->setupInvoiceGeneration(pdfContent: '%PDF content');

        $this->tempFileManager
            ->expects($this->once())
            ->method('delete')
            ->with('/tmp/invoice_abc.pdf');

        $this->handler->handle('ORDER-001');
    }

    public function testHandleThrowsWhenPdfContentIsEmpty(): void
    {
        $this->setupVerifiedUser();
        $this->orderInvoiceQuery->method('getInvoiceDetails')->willReturn([]);
        $this->orderInvoiceAdapter->method('generate')->willReturn('');

        $this->logger->expects($this->once())->method('error');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to read generated PDF file.');

        $this->handler->handle('ORDER-001');
    }

    public function testHandleLogsErrorAndRethrowsWhenQueryThrows(): void
    {
        $exception = new \RuntimeException('Order not found.');

        $this->setupVerifiedUser();
        $this->orderInvoiceQuery->method('getInvoiceDetails')->willThrowException($exception);

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Failed to generate invoice', $exception);

        $this->expectException(\RuntimeException::class);

        $this->handler->handle('ORDER-001');
    }

    public function testHandleLogsErrorWithOrderCodeInContext(): void
    {
        $exception = new \RuntimeException('Order not found.');

        $this->setupVerifiedUser();
        $this->orderInvoiceQuery->method('getInvoiceDetails')->willThrowException($exception);

        $capturedContext = null;

        $this->logger
            ->method('error')
            ->willReturnCallback(
                function (string $message, \Throwable $throwable, mixed $user, array $context) use (&$capturedContext): void {
                    $capturedContext = $context;
                },
            );

        try {
            $this->handler->handle('ORDER-TEST-007');
        } catch (\Throwable) {}

        $this->assertIsArray($capturedContext);
        $this->assertArrayHasKey('code', $capturedContext);
        $this->assertSame('ORDER-TEST-007', $capturedContext['code']);
    }

    public function testHandleThrowsWithoutLoggingWhenPolicyThrows(): void
    {
        $this->securityPolicy
            ->method('checkIfEmailVerified')
            ->willThrowException(new AccessDeniedException('Email not verified.'));

        $this->logger->expects($this->never())->method('error');

        $this->expectException(AccessDeniedException::class);

        $this->handler->handle('ORDER-001');
    }

    private function initMocks(): void
    {
        $this->securityPolicy = $this->createMock(SecurityPolicyContract::class);
        $this->orderInvoiceAdapter = $this->createMock(OrderInvoiceGatewayContract::class);
        $this->orderInvoiceQuery = $this->createMock(OrderInvoiceQueryContract::class);
        $this->tempFileManager = $this->createMock(TempFileManagerContract::class);
        $this->logger = $this->createMock(AppLoggerContract::class);
    }

    private function initHandler(): void
    {
        $this->handler = new GenerateOrderInvoiceHandler(
            $this->securityPolicy,
            $this->orderInvoiceAdapter,
            $this->orderInvoiceQuery,
            $this->tempFileManager,
            $this->logger,
        );
    }

    private function setupInvoiceGeneration(string $pdfContent = '%PDF-1.4 invoice'): void
    {
        $this->setupVerifiedUser();

        $this->orderInvoiceQuery->method('getInvoiceDetails')->willReturn(['order_id' => 1]);
        $this->orderInvoiceAdapter->method('generate')->willReturn($pdfContent);
        $this->tempFileManager->method('create')->willReturn('/tmp/invoice_abc.pdf');
        $this->tempFileManager->method('read')->willReturn($pdfContent);
    }

    private function setupVerifiedUser(): void
    {
        $this->securityPolicy
            ->method('checkIfEmailVerified')
            ->willReturn($this->createMock(User::class));
    }
}
