<?php

declare(strict_types=1);

namespace App\Shared\Traits\Enum;

use Packages\Kit\Utils\Shared\StringNormalizer;

trait HasEnumLabel
{
    public function getLabel(): string
    {
        return $this->transformToLabel($this->value);
    }

    private function transformToLabel(string $value): string
    {
        $value = trim(
            StringNormalizer::toLowerCase($value),
        );

        $value = $this->replaceUnderscoresWithSpaces($value);
        return $this->capitalizeWords($value);
    }

    private function replaceUnderscoresWithSpaces(string $value): string
    {
        return StringNormalizer::replaceUnderscoresWithSpaces($value);
    }

    private function capitalizeWords(string $value): string
    {
        return StringNormalizer::capitalizeWords($value);
    }
}
