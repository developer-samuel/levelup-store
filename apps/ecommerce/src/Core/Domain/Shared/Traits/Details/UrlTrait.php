<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

/**
 * @property string $url
*/
trait UrlTrait
{
    /**
     * @return string
    */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return self
    */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }
}
