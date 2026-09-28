<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Collection;

use Doctrine\Common\Collections\Collection;

/**
 * @template T as object
 *
 * @property Collection<int, T> $items
 */
trait ItemCollectionTrait
{
    /**
     * @return Collection<int, T>
    */
    public function getItems(): Collection
    {
        return $this->items;
    }
}
