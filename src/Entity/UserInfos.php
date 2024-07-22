<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\DateTimeTrait;
use App\Repository\UserInfosRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserInfosRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class UserInfos
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)] // chaine 180 caractères max
    #[Assert\NotBlank()] // empêche la soumission du form vide
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)] // chaine 180 caractères max
    #[Assert\NotBlank()] // empêche la soumission du form vide
    private ?string $lastName = null;

    #[ORM\Column(length: 20)]
    #[Assert\Length(max: 20)] // chaine 20 caractères max
    #[Assert\NotBlank()] // empêche la soumission du form vide
    private ?string $phoneNumber = null;

    #[Vich\UploadableField(mapping: 'photo', fileNameProperty: 'photoName')]
    private ?File $photoFile = null;

    #[Vich\UploadableField(mapping: 'driving_license', fileNameProperty: 'drivingLicenseName')]
    private ?File $drivingLicenseFile = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $photoName = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $drivingLicenseName = null;

    #[ORM\OneToOne(mappedBy: 'UserInfos', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'phoneNumber' => $this->phoneNumber,
            'photoName' => $this->photoName,
            'drivingLicenseName' => $this->drivingLicenseName,
            'user' => $this->user,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->firstName = $data['firstName'] ?? null;
        $this->lastName = $data['lastName'] ?? null;
        $this->phoneNumber = $data['phoneNumber'] ?? null;
        $this->photoName = $data['photoName'] ?? null;
        $this->drivingLicenseName = $data['drivingLicenseName'] ?? null;
        $this->user = $data['user'] ?? null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function setPhotoFile(?File $photoFile = null): void
    {
        $this->photoFile = $photoFile;

        if (null !== $photoFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    public function setPhotoName(?string $photoName): void
    {
        $this->photoName = $photoName;
    }

    public function getPhotoName(): ?string
    {
        return $this->photoName;
    }

    public function setDrivingLicenseFile(?File $drivingLicenseFile = null): void
    {
        $this->drivingLicenseFile = $drivingLicenseFile;

        if (null !== $drivingLicenseFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getDrivingLicenseFile(): ?File
    {
        return $this->drivingLicenseFile;
    }

    public function setDrivingLicenseName(?string $drivingLicenseName): void
    {
        $this->drivingLicenseName = $drivingLicenseName;
    }

    public function getDrivingLicenseName(): ?string
    {
        return $this->drivingLicenseName;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setUserInfos(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getUserInfos() !== $this) {
            $user->setUserInfos($this);
        }

        $this->user = $user;

        return $this;
    }
}
