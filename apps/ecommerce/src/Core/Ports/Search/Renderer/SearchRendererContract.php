<?php

declare(strict_types=1);

namespace App\Core\Ports\Search\Renderer;

interface SearchRendererContract
{
    /**
     * @param array<int, mixed>|null $results
     *
     * @return string
    */
    public function renderIndexView(?array $results): string;

    /**
     * @param array<int, mixed>|null $results
     *
     * @return string
    */
    public function renderSearchPanelView(?array $results): string;
}
