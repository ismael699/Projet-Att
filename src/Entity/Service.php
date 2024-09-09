<?php

namespace App\Entity;

use App\Entity\Annonce;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\DateTimeTrait;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Service
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Annonce>
     */
    #[ORM\OneToMany(targetEntity: Annonce::class, mappedBy: 'service')]
    private Collection $annonces;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
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
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): static
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
            $annonce->setService($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): static
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getService() === $this) {
                $annonce->setService(null);
            }
        }

        return $this;
    }
}
