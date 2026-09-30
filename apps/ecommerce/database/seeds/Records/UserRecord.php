<?php

declare(strict_types=1);

namespace Database\Seeds\Records;

use Database\Seeds\Abstract\AbstractDataRecord;

final class UserRecord extends AbstractDataRecord
{
    /**
     * @return string
    */
    protected function getFilePaths(): string
    {
        return __DIR__ . '/../../data/users.json';
    }
}
