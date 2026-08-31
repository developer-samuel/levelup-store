<?php

declare(strict_types=1);

namespace App\Adapters\External\Api;

use Symfony\{
    Component\HttpFoundation\Response,
    Contracts\HttpClient\HttpClientInterface,
    Contracts\HttpClient\ResponseInterface
};

use Kit\{
    Assertion\Domain\Country\CountryAssertion,
    Utils\Shared\Sanitizer\DataSanitizer
};

use App\Core\Domain\Segment\Country\ValueObject\CountryObject;

use App\Core\Ports\{
    Gateways\External\Api\CountryApiGatewayContract,
    Segment\Country\Repository\CountryRepositoryContract,
    Shared\Logging\AppLoggerContract
};

/**
 * @phpstan-type CountryItem array{
 *     alpha2Code: string,
 *     name: string
 * }
*/
final readonly class CountryApiAdapter implements CountryApiGatewayContract
{
    /**
     * @param HttpClientInterface $httpClient
     * @param AppLoggerContract $logger
     * @param CountryRepositoryContract $countryRepository
     * @param string $countryUrl
    */
    public function __construct(
        private HttpClientInterface $httpClient,
        private AppLoggerContract $logger,
        private CountryRepositoryContract $countryRepository,
        private string $countryUrl,
    ) {}

    /**
     * @return CountryObject[]|null
    */
    public function getAllCountries(): ?array
    {
        $response = $this->makeRequest('GET', $this->countryUrl);

        if ($response === null) {
            return null;
        }

        $data = $this->getResponseData($response);

        $countries = $this->mapToApiCountries($data);

        return $this->sortCountriesByName($countries);
    }

    /**
     * @param string $code
     *
     * @return bool
    */
    public function countryExists(string $code): bool
    {
        $existingCountry = $this->countryRepository->findAllByCode($code);

        return !empty($existingCountry);
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @return ResponseInterface|null
    */
    private function makeRequest(string $method, string $url): ?ResponseInterface
    {
        try {
            $response = $this->httpClient->request($method, $url);

            if (!$this->isResponseSuccessful($response)) {
                $this->logHttpError($response, $method, $url);
                return null;
            }

            return $response;
        } catch (\Throwable $throwable) {
            $this->logger->critical($throwable->getMessage(), $throwable);

            return null;
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @return bool
    */
    private function isResponseSuccessful(ResponseInterface $response): bool
    {
        return $response->getStatusCode() === Response::HTTP_OK;
    }

    /**
     * @param ResponseInterface $response
     * @param string $method
     * @param string $url
     *
     * @return void
    */
    private function logHttpError(ResponseInterface $response, string $method, string $url): void
    {
        $this->logger->error(
            sprintf(
                'HTTP status is not OK: %d for %s %s',
                $response->getStatusCode(),
                $method,
                $url,
            ),
        );
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array<int, CountryItem>
     *
     * @throws \RuntimeException
    */
    private function getResponseData(ResponseInterface $response): array
    {
        try {
            $content = $response->getContent();

            $rawData = json_decode($content, true);
            if (!is_array($rawData)) {
                throw new \RuntimeException('Invalid JSON response.');
            }

            $data = CountryAssertion::assertResponseFormat($rawData);

            return $this->sanitizeAndTypeData($data);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception);

            return [];
        }
    }

    /**
     * @param array<int, CountryItem> $data
     *
     * @return array<int, CountryItem>
    */
    private function sanitizeAndTypeData(array $data): array
    {
        return array_map(
            static function (array $item): array {
                return [
                    'alpha2Code' => DataSanitizer::sanitizeString($item['alpha2Code']),
                    'name'       => DataSanitizer::sanitizeString($item['name']),
                ];
            },
            $data,
        );
    }

    /**
     * @param array<int, CountryItem> $data
     *
     * @return CountryObject[]
    */
    private function mapToApiCountries(array $data): array
    {
        $countries = [];
        foreach ($data as $item) {
            $countries[] = new CountryObject($item['alpha2Code'], $item['name']);
        }

        return $countries;
    }

    /**
     * @param CountryObject[] $countries
     *
     * @return CountryObject[]
    */
    private function sortCountriesByName(array $countries): array
    {
        usort(
            $countries,
            static fn(CountryObject $a, CountryObject $b): int => strcmp($a->name, $b->name),
        );

        return $countries;
    }
}
