<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Banner\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Banner\Enum\BannerType,
    Shared\Traits\Details\ImageTrait,
    Shared\Traits\Details\NameTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\State\ActiveTrait,
    Shared\Traits\State\PositionTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'banners')]
#[ORM\HasLifecycleCallbacks]
class Banner
{
    use IdTrait;
    use PositionTrait;
    use NameTrait;
    use ImageTrait;
    use ActiveTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'smallint')]
    private int $position;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    private ?string $url = null;

    #[ORM\Column(
        type: 'string',
        length: 50,
        enumType: BannerType::class,
        options: ['default' => BannerType::BACKGROUND->value],
    )]
    private BannerType $type = BannerType::BACKGROUND;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isActive = false;

    /**
     * @return BannerType
    */
    public function getType(): BannerType
    {
        return $this->type;
    }

    /**
     * @param BannerType $type
     *
     * @return self
    */
    public function setType(BannerType $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
    */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     *
     * @return self
    */
    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }
}
