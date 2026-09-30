<?php

declare(strict_types=1);

namespace App\Presentation\Search\Renderer;

use Twig\Environment;

use App\Core\Ports\Search\Renderer\SearchRendererContract;

final readonly class SearchRenderer implements SearchRendererContract
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @param array<int, mixed>|null $results
     *
     * @return string
    */
    public function renderIndexView(?array $results): string
    {
        return $this->twig->render(
            'features/search/index.html.twig',
            ['results' => $results ?? []],
        );
    }

    /**
     * @param array<int, mixed>|null $results
     *
     * @return string
    */
    public function renderSearchPanelView(?array $results): string
    {
        return $this->twig->render(
            'layout/public/header/search/panel/card/content/list/list.html.twig',
            ['results' => $results ?? []],
        );
    }
}
