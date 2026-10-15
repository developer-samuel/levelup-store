<?php

declare(strict_types=1);

namespace Database\Seeds\Factories;

use App\Core\Domain\Segment\Synonym\Synonym;

trait SynonymFactory
{
    private function createSynonym(string $synonyms): Synonym
    {
        return (new Synonym())
            ->setSynonyms($synonyms);
    }
}
