<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits;

use Doctrine\Common\Collections\Collection;

use App\Core\Domain\Segment\Product\Entity\ProductSubtype;

/**
 * @property Collection<int, ProductSubtype> $subtypes
*/
trait ProductSubtypeCollectionTrait
{
    /**
     * @return Collection<int, ProductSubtype>
    */
    public function getSubtypes(): Collection
    {
        return $this->subtypes;
    }

    /**
     * @param Collection<int, ProductSubtype> $subtypes
    */
    public function setSubtypes(Collection $subtypes): self
    {
        $this->subtypes = $subtypes;
        return $this;
    }
}
