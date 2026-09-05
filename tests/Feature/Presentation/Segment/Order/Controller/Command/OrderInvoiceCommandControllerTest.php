<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\Segment\Order\Controller\Command;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\{
    Bundle\FrameworkBundle\KernelBrowser,
    Bundle\FrameworkBundle\Test\WebTestCase
};

use PHPUnit\Framework\MockObject\MockObject;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Enum\UserRole
};

use App\Core\Ports\Segment\Order\Handler\Command\GenerateOrderInvoiceHandlerContract;

use Tests\Support\Provides\Persistence;

/**
 * @coversDefaultClass \App\Presentation\Segment\Order\Controller\Command\OrderInvoiceCommandController
*/
class OrderInvoiceCommandControllerTest extends WebTestCase
{
    use Persistence;

    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->getEntityManager();
        $this->em->beginTransaction();

        $this->user = $this->createUser();
    }

    protected function tearDown(): void
    {
        $this->em->rollback();

        parent::tearDown();
    }

    public function testStoreReturnsPdfResponse(): void
    {
        $this->loginWithHandler('%PDF-1.4 fake pdf content');

        $this->client->request('GET', '/orders/ORDER-TEST-001/invoice/download');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/pdf');
    }

    public function testStoreReturnsContentDispositionAttachment(): void
    {
        $this->loginWithHandler('%PDF-1.4 fake pdf content');

        $this->client->request('GET', '/orders/ORDER-ATTACH-001/invoice/download');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString(
            'attachment',
            (string) $this->client->getResponse()->headers->get('Content-Disposition'),
        );
    }

    public function testStoreReturnsPdfBodyContent(): void
    {
        $pdfContent = '%PDF-1.4 test invoice content';

        $this->loginWithHandler($pdfContent);

        $this->client->request('GET', '/orders/ORDER-BODY-001/invoice/download');

        $this->assertSame($pdfContent, $this->client->getResponse()->getContent());
    }

    public function testStoreDelegatesCodeToHandler(): void
    {
        $this->client->loginUser($this->user);

        $handler = $this->createMock(GenerateOrderInvoiceHandlerContract::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with('ORDER-DELEGATE-001')
            ->willReturn('%PDF content');

        static::getContainer()->set(GenerateOrderInvoiceHandlerContract::class, $handler);

        $this->client->request('GET', '/orders/ORDER-DELEGATE-001/invoice/download');

        $this->assertResponseIsSuccessful();
    }

    public function testStoreReturns403WhenNotAuthenticated(): void
    {
        $this->client->request('GET', '/orders/ORDER-NOAUTH-001/invoice/download');

        $this->assertResponseStatusCodeSame(403);
    }

    private function createUser(): User
    {
        $user = (new User())
            ->setEmail('invoice-feature-' . uniqid() . '@test.com')
            ->setFirstName('Test')
            ->setLastName('User')
            ->setPassword('hashed-password')
            ->setRole(UserRole::USER)
            ->setEmailVerifiedAt(new \DateTimeImmutable());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function loginWithHandler(string $pdfContent): void
    {
        $this->client->loginUser($this->user);

        static::getContainer()->set(
            GenerateOrderInvoiceHandlerContract::class,
            $this->createHandlerMock($pdfContent),
        );
    }

    private function createHandlerMock(string $pdfContent): GenerateOrderInvoiceHandlerContract&MockObject
    {
        $handler = $this->createMock(GenerateOrderInvoiceHandlerContract::class);
        $handler->method('handle')->willReturn($pdfContent);

        return $handler;
    }
}
