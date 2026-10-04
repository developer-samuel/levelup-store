<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

trait UrlTrait
{
    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }
}
