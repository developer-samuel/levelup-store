<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Footer\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Footer\Enum\FooterLinkGroup,
    Segment\Footer\Enum\FooterLinkTarget,
    Shared\Traits\Details\ImageTrait,
    Shared\Traits\Details\UrlTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\State\PositionTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'footer_links')]
#[ORM\HasLifecycleCallbacks]
class FooterLink
{
    use IdTrait;
    use PositionTrait;
    use ImageTrait;
    use UrlTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    private int $position;

    #[ORM\Column(type: 'string', length: 100)]
    private string $value;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $url;

    #[ORM\Column(name: 'link_group', type: 'string', length: 20, enumType: FooterLinkGroup::class)]
    private FooterLinkGroup $group;

    #[ORM\Column(
        name: 'link_target',
        type: 'string',
        length: 10,
        enumType: FooterLinkTarget::class,
        options: ['default' => FooterLinkTarget::BLANK->value],
    )]
    private FooterLinkTarget $target = FooterLinkTarget::BLANK;

    /**
     * @return string
    */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return self
    */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return FooterLinkGroup
    */
    public function getGroup(): FooterLinkGroup
    {
        return $this->group;
    }

    /**
     * @param FooterLinkGroup $group
     *
     * @return self
    */
    public function setGroup(FooterLinkGroup $group): self
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return FooterLinkTarget
    */
    public function getTarget(): FooterLinkTarget
    {
        return $this->target;
    }

    /**
     * @param FooterLinkTarget $target
     *
     * @return self
    */
    public function setTarget(FooterLinkTarget $target): self
    {
        $this->target = $target;
        return $this;
    }
}
