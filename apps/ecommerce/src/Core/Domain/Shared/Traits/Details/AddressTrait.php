<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Country\Entity\Country,
    Shared\Traits\Identity\IdTrait,
};

trait AddressTrait
{
    use IdTrait;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    protected ?string $street = null;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    protected ?string $postalCode = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    protected ?string $city = null;

    #[ORM\OneToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(
        name: 'country_id',
        referencedColumnName: 'id',
        onDelete: 'CASCADE',
        nullable: true,
    )]
    protected ?Country $country = null;

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }
}
