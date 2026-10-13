<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Synonym;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\Shared\Traits\{
    Identity\IdTrait,
    Timestamps\CreatedTimestampTrait,
    Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'search_synonyms')]
#[ORM\HasLifecycleCallbacks]
class Synonym
{
    use IdTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $synonyms;

    public function getSynonyms(): string
    {
        return $this->synonyms;
    }

    public function setSynonyms(string $synonyms): self
    {
        $this->synonyms = $synonyms;

        return $this;
    }
}
