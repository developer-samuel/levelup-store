<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Subtype\Traits;

use Doctrine\Common\Collections\Collection;

use App\Core\Domain\Segment\Subtype\Entity\Subtype;

/**
 * @property Collection<int, Subtype> $subtypes
*/
trait SubtypeCollectionTrait
{
    /**
     * @return Collection<int, Subtype>
    */
    public function getSubtypes(): Collection
    {
        return $this->subtypes;
    }

    /**
     * @param Collection<int, Subtype> $subtypes
    */
    public function setSubtypes(Collection $subtypes): self
    {
        $this->subtypes = $subtypes;
        return $this;
    }
}
