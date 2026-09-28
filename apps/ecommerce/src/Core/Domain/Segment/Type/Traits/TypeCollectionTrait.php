<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Type\Traits;

use Doctrine\Common\Collections\Collection;

use App\Core\Domain\Segment\Type\Entity\Type;

/**
 * @property Collection<int, Type> $types
*/
trait TypeCollectionTrait
{
    /**
     * @return Collection<int, Type>
    */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    /**
     * @param Collection<int, Type> $types
     *
     * @return self
    */
    public function setTypes(Collection $types): self
    {
        $this->types = $types;
        return $this;
    }
}
