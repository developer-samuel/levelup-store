<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\Segment\Order\Controller;

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

use App\Core\Ports\Segment\Order\Handler\Command\CreateOrderHandlerContract;

use Tests\{
    Support\Provides\DecodesJson,
    Support\Provides\Persistence
};

/**
 * @coversDefaultClass \App\Presentation\Segment\Order\Controller\Command\OrderCommandController
*/
class OrderCommandControllerTest extends WebTestCase
{
    use DecodesJson;
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

    public function testStoreReturnsCashOrderSuccessRedirect(): void
    {
        $this->loginWithHandler(['status' => 'success', 'redirect_route' => 'orders_success']);

        $this->client->request('POST', '/orders/store', $this->buildOrderPayload('cash'));

        $this->assertResponseIsSuccessful();

        $data = $this->decodeJson();

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('redirect', $data);
    }

    public function testStoreReturnsCardOrderWithPaymentUrl(): void
    {
        $this->loginWithHandler(['status' => 'success', 'redirect' => 'https://checkout.stripe.com/pay/cs_test_abc']);

        $this->client->request('POST', '/orders/store', $this->buildOrderPayload('card'));

        $this->assertResponseIsSuccessful();

        $data = $this->decodeJson();

        $this->assertTrue($data['success']);
    }

    public function testStoreReturnsJsonResponse(): void
    {
        $this->loginWithHandler(['status' => 'success', 'redirect_route' => 'orders_success']);

        $this->client->request('POST', '/orders/store', $this->buildOrderPayload());

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testStoreReturnsUnprocessableOnValidationErrors(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('POST', '/orders/store', []);

        $this->assertResponseStatusCodeSame(422);

        $data = $this->decodeJson();

        $this->assertFalse($data['success']);
        $this->assertNotEmpty($data['errors']);
    }

    public function testStoreWithShippingCallsCreateShippingObject(): void
    {
        $this->loginWithHandler(['status' => 'success', 'redirect_route' => 'orders_success']);

        $this->client->request('POST', '/orders/store', array_merge(
            $this->buildOrderPayload('cash'),
            [
                'send_shipping'        => '1',
                'shipping_country'     => '1',
                'shipping_street'      => 'Ship St 5',
                'shipping_postal_code' => '99999',
                'shipping_city'        => 'Košice',
            ],
        ));

        $this->assertResponseIsSuccessful();
    }

    public function testStoreReturnsServerErrorOnInvalidPaymentMethod(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('POST', '/orders/store', $this->buildOrderPayload('bitcoin'));

        $this->assertResponseStatusCodeSame(500);
    }

    public function testStoreReturns403WhenNotAuthenticated(): void
    {
        $this->client->request('POST', '/orders/store', $this->buildOrderPayload());

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @param array<string, mixed> $returnValue
    */
    private function loginWithHandler(array $returnValue): void
    {
        $this->client->loginUser($this->user);

        static::getContainer()->set(
            CreateOrderHandlerContract::class,
            $this->createHandlerMock($returnValue),
        );
    }

    private function createUser(): User
    {
        $user = (new User())
            ->setEmail('order-feature-' . uniqid() . '@test.com')
            ->setFirstName('Test')
            ->setLastName('User')
            ->setPassword('hashed-password')
            ->setRole(UserRole::USER)
            ->setEmailVerifiedAt(new \DateTimeImmutable());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @return array<string, mixed>
    */
    private function buildOrderPayload(string $paymentMethod = 'card'): array
    {
        return [
            'email'               => 'test@example.com',
            'first_name'          => 'John',
            'last_name'           => 'Doe',
            'payment_method'      => $paymentMethod,
            'send_shipping'       => '0',
            'billing_country'     => '1',
            'billing_street'      => 'Main St 1',
            'billing_postal_code' => '12345',
            'billing_city'        => 'Bratislava',
        ];
    }

    /**
     * @param array<string, mixed> $returnValue
    */
    private function createHandlerMock(array $returnValue): CreateOrderHandlerContract&MockObject
    {
        $handler = $this->createMock(CreateOrderHandlerContract::class);
        $handler->method('handle')->willReturn($returnValue);

        return $handler;
    }
}
