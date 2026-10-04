<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Subtype\Traits;

use App\Core\Domain\Segment\Subtype\Entity\Subtype;

trait SubtypeTrait
{
    public function getSubtype(): Subtype
    {
        return $this->subtype;
    }

    public function setSubtype(Subtype $subtype): self
    {
        $this->subtype = $subtype;
        return $this;
    }
}
