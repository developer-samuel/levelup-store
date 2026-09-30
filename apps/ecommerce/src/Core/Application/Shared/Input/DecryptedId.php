<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Input;

trait DecryptedId
{
    private int $decryptedId;

    /**
     * @return int
    */
    public function getDecryptedId(): int
    {
        return $this->decryptedId;
    }
}
