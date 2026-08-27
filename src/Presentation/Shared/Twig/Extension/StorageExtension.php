<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Twig\Extension;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};

use App\Core\Ports\Gateways\External\Storage\StorageGatewayContract;

final class StorageExtension extends AbstractExtension
{
    /**
     * @param StorageGatewayContract $storage
    */
    public function __construct(
        private readonly StorageGatewayContract $storage,
    ) {}

    /**
     * @return TwigFunction[]
    */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('storage_url', function (?string $path): string {
                if ($path === null) {
                    return '/img/misc/image/no-image.webp';
                }

                $normalized = ltrim($path, '/');

                if (str_starts_with($normalized, 'img/')) {
                    return '/' . $normalized;
                }

                return $this->storage->url($path);
            }),
        ];
    }
}
