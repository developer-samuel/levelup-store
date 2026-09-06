<?php

declare(strict_types=1);

namespace App\Adapters\External\Turnstile;

use Symfony\Contracts\HttpClient\HttpClientInterface;

use App\Core\Ports\Gateways\External\Turnstile\TurnstileGatewayContract;

final readonly class TurnstileAdapter implements TurnstileGatewayContract
{
    /**
     * @param HttpClientInterface $httpClient
     * @param bool   $enabled
     * @param string $secretKey
     * @param string $verifyUrl
    */
    public function __construct(
        private HttpClientInterface $httpClient,
        private bool $enabled,
        private string $secretKey,
        private string $verifyUrl,
    ) {}

    /**
     * @return bool
    */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param string $token
     * @param string $ip
     *
     * @return bool
    */
    public function verify(string $token, string $ip): bool
    {
        if (!$this->enabled) {
            return true;
        }

        if ($token === '') {
            return false;
        }

        try {
            $response = $this->httpClient->request('POST', $this->verifyUrl, [
                'body' => [
                    'secret'   => $this->secretKey,
                    'response' => $token,
                    'remoteip' => $ip,
                ],
            ]);

            /** @var array{success: bool} $data */
            $data = $response->toArray();

            return $data['success'] === true;
        } catch (\Throwable) {
            return false;
        }
    }
}
