<?php

declare(strict_types=1);

namespace Database\Seeds\Records\Contracts;

interface ProductRecordContract
{
    /**
     * @return array<string, array<string, mixed>>
    */
    public function fetchData(): array;
}
