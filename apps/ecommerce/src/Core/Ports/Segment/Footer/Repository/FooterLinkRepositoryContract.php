<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Footer\Repository;

use App\Core\Domain\{
    Segment\Footer\Entity\FooterLink,
    Segment\Footer\Enum\FooterLinkGroup
};

interface FooterLinkRepositoryContract
{
    /**
     * @return FooterLink[]
    */
    public function findAllOrderedByGroup(): array;

    /**
     * @param FooterLinkGroup $group
     *
     * @return FooterLink[]
    */
    public function findByGroup(FooterLinkGroup $group): array;
}
