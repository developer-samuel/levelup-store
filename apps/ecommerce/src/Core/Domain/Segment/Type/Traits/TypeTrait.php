<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Type\Traits;

use App\Core\Domain\Segment\Type\Entity\Type;

trait TypeTrait
{
    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }
}
