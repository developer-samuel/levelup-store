<?php

declare(strict_types=1);

namespace App\Shared\Traits\Enum;

use Kit\Utils\Shared\StringNormalizer;

/**
 * @property string $value
 */
trait HasEnumLabel
{
    /**
     * @return string
    */
    public function getLabel(): string
    {
        return $this->transformToLabel($this->value);
    }

    /**
     * @param string $value
     *
     * @return string
    */
    private function transformToLabel(string $value): string
    {
        $value = trim(
            StringNormalizer::toLowerCase($value),
        );

        $value = $this->replaceUnderscoresWithSpaces($value);
        return $this->capitalizeWords($value);
    }

    /**
     * @param string $value
     *
     * @return string
    */
    private function replaceUnderscoresWithSpaces(string $value): string
    {
        return StringNormalizer::replaceUnderscoresWithSpaces($value);
    }

    /**
     * @param string $value
     *
     * @return string
    */
    private function capitalizeWords(string $value): string
    {
        return StringNormalizer::capitalizeWords($value);
    }
}
