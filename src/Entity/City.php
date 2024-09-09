<?php

namespace App\Entity;

use App\Entity\Annonce;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CityRepository;
use App\Entity\Traits\DateTimeTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ORM\HasLifecycleCallbacks]
class City
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 10)]
    private $code;

    /**
     * @var Collection<int, Annonce>
     */
    #[ORM\ManyToMany(targetEntity: Annonce::class, inversedBy: 'cities')]
    private Collection $Annonce;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->Annonce = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnonce(): Collection
    {
        return $this->Annonce;
    }

    public function addAnnonce(Annonce $annonce): static
    {
        if (!$this->Annonce->contains($annonce)) {
            $this->Annonce->add($annonce);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): static
    {
        $this->Annonce->removeElement($annonce);

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }
}

