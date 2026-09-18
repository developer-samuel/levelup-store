<?php

declare(strict_types=1);

namespace Tests\Integration\Adapters\External\Api\Country;

use Symfony\{
    Component\HttpFoundation\Response,
    Contracts\HttpClient\HttpClientInterface,
    Contracts\HttpClient\ResponseInterface
};

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\Segment\Country\ValueObject\CountryObject;

use App\Core\Ports\{
    Segment\Country\CountryRepositoryContract,
    Shared\Logging\AppLoggerContract
};

use App\Adapters\External\Api\CountryApiAdapter;

/**
 * @coversDefaultClass \App\Adapters\External\Api\CountryApiAdapter
*/
final class CountryApiAdapterTest extends TestCase
{
    private HttpClientInterface&MockObject $httpClient;
    private AppLoggerContract&MockObject $logger;
    private CountryRepositoryContract&MockObject $countryRepository;
    private CountryApiAdapter $adapter;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initAdapter();
    }

    public function testGetAllCountriesReturnsSortedCountries(): void
    {
        $json = json_encode([
            ['alpha2Code' => 'SK', 'name' => 'Slovakia'],
            ['alpha2Code' => 'CZ', 'name' => 'Czech Republic'],
            ['alpha2Code' => 'AT', 'name' => 'Austria'],
        ]);

        $this->mockHttpRequest(Response::HTTP_OK, (string) $json);

        $result = $this->adapter->getAllCountries();

        self::assertNotNull($result);
        self::assertCount(3, $result);
        self::assertContainsOnlyInstancesOf(CountryObject::class, $result);

        self::assertSame('Austria', $result[0]->name);
        self::assertSame('Czech Republic', $result[1]->name);
        self::assertSame('Slovakia', $result[2]->name);
    }

    public function testGetAllCountriesReturnsNullOnHttpError(): void
    {
        $this->mockHttpRequest(Response::HTTP_INTERNAL_SERVER_ERROR, '');

        $this->logger
            ->expects(self::once())
            ->method('error');

        $result = $this->adapter->getAllCountries();

        self::assertNull($result);
    }

    public function testGetAllCountriesReturnsNullOnNetworkException(): void
    {
        $this->httpClient
            ->method('request')
            ->willThrowException(new \RuntimeException('Connection refused'));

        $this->logger
            ->expects(self::once())
            ->method('critical');

        $result = $this->adapter->getAllCountries();

        self::assertNull($result);
    }

    public function testGetAllCountriesReturnsEmptyArrayOnInvalidJson(): void
    {
        $this->mockHttpRequest(Response::HTTP_OK, 'not-valid-json');

        $this->logger
            ->expects(self::once())
            ->method('error');

        $result = $this->adapter->getAllCountries();

        self::assertIsArray($result);
        self::assertEmpty($result);
    }

    public function testGetAllCountriesReturnsEmptyArrayOnNonListJson(): void
    {
        $this->mockHttpRequest(Response::HTTP_OK, '{"key": "value"}');

        $this->logger
            ->expects(self::once())
            ->method('error');

        $result = $this->adapter->getAllCountries();

        self::assertIsArray($result);
        self::assertEmpty($result);
    }

    public function testGetAllCountriesReturnsEmptyArrayOnMissingFields(): void
    {
        $json = json_encode([['alpha2Code' => 'SK']]);

        $this->mockHttpRequest(Response::HTTP_OK, (string) $json);

        $this->logger
            ->expects(self::once())
            ->method('error');

        $result = $this->adapter->getAllCountries();

        self::assertIsArray($result);
        self::assertEmpty($result);
    }

    public function testGetAllCountriesReturnsSingleCountry(): void
    {
        $json = json_encode([['alpha2Code' => 'DE', 'name' => 'Germany']]);

        $this->mockHttpRequest(Response::HTTP_OK, (string) $json);

        $result = $this->adapter->getAllCountries();

        self::assertNotNull($result);
        self::assertCount(1, $result);
        self::assertSame('DE', $result[0]->code);
        self::assertSame('Germany', $result[0]->name);
    }

    public function testGetAllCountriesReturnsEmptyArrayOnEmptyList(): void
    {
        $this->mockHttpRequest(Response::HTTP_OK, '[]');

        $result = $this->adapter->getAllCountries();

        self::assertIsArray($result);
        self::assertEmpty($result);
    }

    public function testGetAllCountriesSanitizesWhitespace(): void
    {
        $json = json_encode([['alpha2Code' => '  SK  ', 'name' => '  Slovakia  ']]);

        $this->mockHttpRequest(Response::HTTP_OK, (string) $json);

        $result = $this->adapter->getAllCountries();

        self::assertNotNull($result);
        self::assertCount(1, $result);
        self::assertSame('SK', $result[0]->code);
        self::assertSame('Slovakia', $result[0]->name);
    }

    public function testCountryExistsReturnsTrueWhenFound(): void
    {
        $this->countryRepository
            ->method('findAllByCode')
            ->with('SK')
            ->willReturn(['some-country']);

        $result = $this->adapter->countryExists('SK');

        self::assertTrue($result);
    }

    public function testCountryExistsReturnsFalseWhenNotFound(): void
    {
        $this->countryRepository
            ->method('findAllByCode')
            ->with('XX')
            ->willReturn([]);

        $result = $this->adapter->countryExists('XX');

        self::assertFalse($result);
    }

    private function initMocks(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->logger = $this->createMock(AppLoggerContract::class);
        $this->countryRepository = $this->createMock(CountryRepositoryContract::class);
    }

    private function initAdapter(): void
    {
        $this->adapter = new CountryApiAdapter(
            $this->httpClient,
            $this->logger,
            $this->countryRepository,
            'https://api.example.com/countries',
        );
    }

    private function mockHttpRequest(int $statusCode, string $body): void
    {
        $this->httpClient
            ->method('request')
            ->willReturn($this->mockResponse($statusCode, $body));
    }

    private function mockResponse(int $statusCode, string $content): ResponseInterface&MockObject
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('getContent')->willReturn($content);

        return $response;
    }
}
