<?php

declare(strict_types=1);

namespace Database\Seeds\Utils\Resolver;

final class PathResolver
{
    /**
     * @param string $folder
     * @param string $jsonFile
     *
     * @return string[]
    */
    public static function fromJson(string $folder, string $jsonFile): array
    {
        $content = self::readJsonFile($jsonFile);
        /** @var mixed[] $decoded */
        $decoded = self::decodeJson($content, $jsonFile);
        $decodedStrings = self::filterAndCastToStrings($decoded);

        return self::prependFolder($decodedStrings, $folder);
    }

    /**
     * @param string $jsonFile
     *
     * @return string
     *
     * @throws \RuntimeException
    */
    private static function readJsonFile(string $jsonFile): string
    {
        if (!file_exists($jsonFile)) {
            self::throwRuntime('JSON file not found', $jsonFile);
        }

        $content = file_get_contents($jsonFile);
        if ($content === false) {
            self::throwRuntime('Failed to read JSON file', $jsonFile);
        }

        /** @var string $content */
        return $content;
    }

    /**
     * @param string $content
     * @param string $jsonFile
     *
     * @return mixed[]
     *
     * @throws \RuntimeException
    */
    private static function decodeJson(string $content, string $jsonFile): array
    {
        $decoded = json_decode($content, true);

        if (!is_array($decoded)) {
            self::throwRuntime('JSON file does not contain an array', $jsonFile);
        }

        /** @var mixed[] $decoded */
        return $decoded;
    }

    /**
     * @param mixed[] $decoded
     *
     * @return string[]
    */
    private static function filterAndCastToStrings(array $decoded): array
    {
        $strings = [];

        foreach ($decoded as $item) {
            if (is_string($item) || is_int($item) || is_float($item)) {
                $strings[] = (string) $item;
            }
        }

        return $strings;
    }

    /**
     * @param string[] $files
     * @param string $folder
     *
     * @return string[]
    */
    private static function prependFolder(array $files, string $folder): array
    {
        return array_map(
            static fn(string $file): string => $folder . $file,
            $files,
        );
    }

    /**
     * @param string $message
     * @param string|null $file
     *
     * @return void
     *
     * @throws \RuntimeException
    */
    private static function throwRuntime(string $message, ?string $file = null): void
    {
        if ($file !== null) {
            $message .= ': ' . $file;
        }

        throw new \RuntimeException($message);
    }
}
