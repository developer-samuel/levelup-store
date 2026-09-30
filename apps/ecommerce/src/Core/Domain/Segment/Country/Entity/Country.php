<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Country\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Shared\Traits\Identity\CodeTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Details\NameTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'countries')]
#[ORM\HasLifecycleCallbacks]
class Country
{
    use IdTrait;
    use CodeTrait;
    use NameTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'string', unique: true, length: 2, nullable: false)]
    private string $code;

    #[ORM\Column(type: 'string', unique: true, length: 100, nullable: false)]
    private string $name;
}
