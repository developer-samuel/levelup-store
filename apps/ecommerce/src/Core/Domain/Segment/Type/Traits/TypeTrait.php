<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Type\Traits;

use App\Core\Domain\Segment\Type\Entity\Type;

/**
 * @property Type $type
*/
trait TypeTrait
{
    /**
     * @return Type
    */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @param Type $type
     *
     * @return self
    */
    public function setType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }
}
