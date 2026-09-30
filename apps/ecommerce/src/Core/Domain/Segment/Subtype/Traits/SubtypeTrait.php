<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Subtype\Traits;

use App\Core\Domain\Segment\Subtype\Entity\Subtype;

/**
 * @property Subtype $subtype
*/
trait SubtypeTrait
{
    /**
     * @return Subtype
    */
    public function getSubtype(): Subtype
    {
        return $this->subtype;
    }

    /**
     * @param Subtype $subtype
     *
     * @return self
    */
    public function setSubtype(Subtype $subtype): self
    {
        $this->subtype = $subtype;
        return $this;
    }
}
