<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Twig\Extension;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};

use App\Core\Ports\Shared\Encryption\HmacGeneratorContract;

final class HmacExtension extends AbstractExtension
{
    /**
     * @param HmacGeneratorContract $hmacGenerator
    */
    public function __construct(
        private readonly HmacGeneratorContract $hmacGenerator,
    ) {}

    /**
     * @return TwigFunction[]
    */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'hmac_encrypt',
                fn(int $value): string => $this->encryptValue($value),
            ),
        ];
    }

    /**
     * @param int $value
     *
     * @return string
    */
    private function encryptValue(int $value): string
    {
        return $this->hmacGenerator->encrypt($value);
    }
}
