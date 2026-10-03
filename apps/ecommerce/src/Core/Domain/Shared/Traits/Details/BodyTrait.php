<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

trait BodyTrait
{
    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }
}
