<?php

declare(strict_types=1);

namespace Database\Seeds\Factories;

use App\Core\Domain\{
    Segment\Footer\Entity\FooterLink,
    Segment\Footer\Enum\FooterLinkGroup,
    Segment\Footer\Enum\FooterLinkTarget
};

trait FooterLinkFactory
{
    private function createFooterLink(
        int $position,
        string $value,
        ?string $image,
        string $url,
        FooterLinkGroup $group,
        FooterLinkTarget $target,
    ): FooterLink {
        return (new FooterLink())
            ->setPosition($position)
            ->setValue($value)
            ->setImage($image)
            ->setUrl($url)
            ->setGroup($group)
            ->setTarget($target);
    }
}
