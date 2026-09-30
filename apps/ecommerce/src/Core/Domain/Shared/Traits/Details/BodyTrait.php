<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

/**
 * @property string $body
*/
trait BodyTrait
{
    /**
     * @return string
    */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return self
    */
    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }
}
