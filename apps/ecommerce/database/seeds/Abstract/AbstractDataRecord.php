<?php

declare(strict_types=1);

namespace Database\Seeds\Abstract;

abstract class AbstractDataRecord
{
    /**
     * @return string|string[]
     */
    abstract protected function getFilePaths(): string|array;

    /**
     * @return array<string, string[]>
     */
    final public function fetchData(): array
    {
        $filePaths = $this->normalizeFilePaths($this->getFilePaths());
        $mergedData = [];

        foreach ($filePaths as $filePath) {
            $data = $this->loadJsonFile($filePath);
            $mergedData = $this->mergeData($mergedData, $data);
        }

        return $mergedData;
    }

    /**
     * @param string|string[] $filePaths
     *
     * @return string[]
     */
    private function normalizeFilePaths(string|array $filePaths): array
    {
        return is_array($filePaths) ? $filePaths : [$filePaths];
    }

    /**
     * @param string $filePath
     *
     * @return array<string, string[]>
     *
     * @throws \RuntimeException
     */
    private function loadJsonFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException(sprintf('JSON file not found: %s', $filePath));
        }

        $jsonContent = file_get_contents($filePath);
        if ($jsonContent === false) {
            throw new \RuntimeException(sprintf('Failed to read JSON file: %s', $filePath));
        }

        $data = json_decode($jsonContent, true);

        if (!is_array($data)) {
            throw new \RuntimeException(sprintf('Invalid JSON format in file: %s', $filePath));
        }

        /** @var array<string, string[]> $data */
        return $data;
    }

    /**
     * @param array<string, string[]> $original
     * @param array<string, string[]> $newData
     *
     * @return array<string, string[]>
     */
    private function mergeData(array $original, array $newData): array
    {
        foreach ($newData as $key => $values) {
            if (!isset($original[$key])) {
                $original[$key] = $values;
                continue;
            }

            $original[$key] = array_merge($original[$key], $values);
        }

        return $original;
    }
}
