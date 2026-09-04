<?php

declare(strict_types=1);

namespace App\Presentation\Dev;

use Doctrine\DBAL\Connection;

use Stripe\{
    Stripe,
    StripeClient
};

use Symfony\{
    Bundle\FrameworkBundle\Controller\AbstractController,
    Component\HttpFoundation\JsonResponse,
    Contracts\Cache\CacheInterface,
    Contracts\Cache\ItemInterface
};

use App\Core\Ports\{
    Gateways\External\MessageBroker\RabbitMQGatewayContract,
    Gateways\External\Realtime\MercureHubGatewayContract,
    Gateways\External\Search\ElasticsearchGatewayContract,
    Gateways\External\Storage\StorageGatewayContract
};

class HealthCheckController extends AbstractController
{
    private const DISK_MIN_FREE_BYTES = 1024 * 1024 * 1024;
    private const MAILER_TIMEOUT      = 3;

    /**
     * @param Connection $connection
     * @param CacheInterface $cache
     * @param RabbitMQGatewayContract $rabbitMQ
     * @param ElasticsearchGatewayContract $elasticsearch
     * @param StorageGatewayContract $storage
     * @param MercureHubGatewayContract $mercure
     * @param HealthCheckConfig $config
    */
    public function __construct(
        private readonly Connection $connection,
        private readonly CacheInterface $cache,
        private readonly RabbitMQGatewayContract $rabbitMQ,
        private readonly ElasticsearchGatewayContract $elasticsearch,
        private readonly StorageGatewayContract $storage,
        private readonly MercureHubGatewayContract $mercure,
        private readonly HealthCheckConfig $config,
    ) {}

    /**
     * @return JsonResponse
    */
    public function check(): JsonResponse
    {
        $checks = [
            'database'      => $this->checkDatabase(),
            'cache'         => $this->checkCache(),
            'disk'          => $this->checkDisk(),
            'mailer'        => $this->checkMailer(),
            'stripe'        => $this->checkStripe(),
            'rabbitmq'      => $this->checkRabbitMQ(),
            'elasticsearch' => $this->checkElasticsearch(),
            'minio'         => $this->checkMinIO(),
            'mercure'       => $this->checkMercure(),
        ];

        $status = in_array('error', $checks, true) ? 'error' : 'ok';

        return new JsonResponse([
            'status'      => $status,
            ...$checks,
            'wkhtmltopdf' => $this->checkWkhtmltopdf(),
        ]);
    }

    /**
     * @return string
    */
    private function checkDatabase(): string
    {
        try {
            $this->connection->executeQuery('SELECT 1');

            return 'ok';
        } catch (\Throwable) {
            return 'error';
        }
    }

    /**
     * @return string
    */
    private function checkCache(): string
    {
        try {
            $this->cache->get('health_check', static function (ItemInterface $item): string {
                $item->expiresAfter(10);

                return 'ok';
            });

            return 'ok';
        } catch (\Throwable) {
            return 'error';
        }
    }

    /**
     * @return string
    */
    private function checkDisk(): string
    {
        $free = disk_free_space('/');

        if ($free === false || $free < self::DISK_MIN_FREE_BYTES) {
            return 'error';
        }

        return 'ok';
    }

    /**
     * @return string
    */
    private function checkMailer(): string
    {
        $connection = fsockopen(
            'ssl://' . $this->config->mailerHost,
            $this->config->mailerPort,
            timeout: self::MAILER_TIMEOUT,
        );

        if ($connection === false) {
            return 'error';
        }

        try {
            // Read server greeting
            fgets($connection);

            // EHLO handshake
            fwrite($connection, "EHLO healthcheck\r\n");
            while ($line = fgets($connection)) {
                if (str_starts_with($line, '250 ')) {
                    break;
                }
            }

            // AUTH LOGIN
            fwrite($connection, "AUTH LOGIN\r\n");
            fgets($connection); // 334 username prompt

            fwrite($connection, base64_encode($this->config->mailerUser) . "\r\n");
            fgets($connection); // 334 password prompt

            fwrite($connection, base64_encode(urldecode($this->config->mailerPass)) . "\r\n");
            $authResponse = fgets($connection);

            // QUIT
            fwrite($connection, "QUIT\r\n");

            return str_starts_with((string) $authResponse, '235') ? 'ok' : 'error';
        } finally {
            fclose($connection);
        }
    }

    /**
     * @return string
    */
    private function checkStripe(): string
    {
        try {
            Stripe::setApiKey($this->config->stripeSecretKey);

            $client = new StripeClient($this->config->stripeSecretKey);
            $client->balance->retrieve();

            return 'ok';
        } catch (\Throwable) {
            return 'error';
        }
    }

    /**
     * @return string
    */
    private function checkRabbitMQ(): string
    {
        if (!$this->rabbitMQ->isEnabled()) {
            return 'disabled';
        }

        return $this->rabbitMQ->isConnected() ? 'ok' : 'error';
    }

    /**
     * @return string
    */
    private function checkElasticsearch(): string
    {
        if (!$this->elasticsearch->isEnabled()) {
            return 'disabled';
        }

        return $this->elasticsearch->isConnected() ? 'ok' : 'error';
    }

    /**
     * @return string
    */
    private function checkMinIO(): string
    {
        if (!$this->storage->isEnabled()) {
            return 'disabled';
        }

        return $this->storage->isConnected() ? 'ok' : 'error';
    }

    /**
     * @return string
    */
    private function checkMercure(): string
    {
        if (!$this->mercure->isEnabled()) {
            return 'disabled';
        }

        return $this->mercure->isConnected() ? 'ok' : 'error';
    }

    /**
     * @return string
    */
    private function checkWkhtmltopdf(): string
    {
        if (!$this->config->wkhtmltopdfEnabled) {
            return 'disabled';
        }

        return file_exists($this->config->wkhtmltopdfPath) ? 'ok' : 'error';
    }
}
