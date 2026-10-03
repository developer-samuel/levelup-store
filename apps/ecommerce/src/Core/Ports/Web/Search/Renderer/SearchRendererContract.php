<?php

declare(strict_types=1);

namespace App\Core\Ports\Web\Search\Renderer;

interface SearchRendererContract
{
    /** @param array<int, mixed>|null $results */
    public function renderIndexView(?array $results): string;

    /** @param array<int, mixed>|null $results */
    public function renderSearchPanelView(?array $results): string;
}
