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

    #[ORM\Column(length: 20)]
    #[Assert\Length(max: 20)] // chaine 20 caractères max
    #[Assert\NotBlank()] // empêche la soumission du form vide
    private ?string $phoneNumber = null;

    #[Vich\UploadableField(mapping: 'photo', fileNameProperty: 'photoName')]
    private ?File $photoFile = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $drivingLicenseName = null;

    #[Vich\UploadableField(mapping: 'driving_license', fileNameProperty: 'drivingLicenseName')]
    private ?File $drivingLicenseFile = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $photoName = null;

    #[Vich\UploadableField(mapping: 'fichier', fileNameProperty: 'fileName')]
    private ?File $File = null;

    #[ORM\Column(nullable: true)]
    private ?string $fileName = null;

    #[ORM\OneToOne(targetEntity:User::class, mappedBy: 'UserInfos', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'phoneNumber' => $this->phoneNumber,
            'photoName' => $this->photoName,
            'drivingLicenseName' => $this->drivingLicenseName,
            'fileName' => $this->fileName,
            'user' => $this->user,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->phoneNumber = $data['phoneNumber'] ?? null;
        $this->photoName = $data['photoName'] ?? null;
        $this->drivingLicenseName = $data['drivingLicenseName'] ?? null;
        $this->fileName = $data['fileName'] ?? null;
        $this->user = $data['user'] ?? null;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setFile(?File $File = null): void
    {
        $this->File = $File;

        if (null !== $File) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getFile(): ?File
    {
        return $this->File;
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }
}
