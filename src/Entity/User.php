<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Entity\Traits\DateTimeTrait;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])] // pour ne pas avoir plusieurs fois le même email en BDD
#[UniqueEntity(fields: ['siren'], message: 'Informations déjà existantes')] // assure que çe champ reste unique en bdd
#[ORM\HasLifecycleCallbacks] // ?
#[Uploadable] // indique que cette entité est "uploadable"
class User implements UserInterface, PasswordAuthenticatedUserInterface // ?
{
    use DateTimeTrait; // ?

    #[ORM\Id]
    #[ORM\GeneratedValue] // génère automatiquement l'id en bdd
    #[ORM\Column] // correspond à une colonne en bdd
    private ?int $id = null;

    #[ORM\Column(length: 180)] // colonne de max 180 caractères
    #[Assert\Length(max: 180)] // chaine 180 caractères max
    #[Assert\NotBlank()] // empêche la soumission du form vide
    #[Assert\Email] // oblige l'utilisateur à mettre un email valide
    private ?string $email = null;

     /**
     * @var list<string> 
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string 
     */
    #[ORM\Column]
    #[Assert\NotBlank()] // empêche la soumission du form vide
    private ?string $password = null;

    #[ORM\Column(length: 9)]
    #[Assert\NotBlank()] // empêche la soumission du form vide
    private ?string $siren = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])] // pareil qu'en haut
    private ?Payment $payment = null;

    #[Vich\UploadableField(mapping: 'fichier', fileNameProperty: 'fileName', size: 'fileSize')]
    private ?File $File = null;

    #[ORM\Column(nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(nullable: true)]
    private ?int $fileSize = null; // représente la taille du fichier en octets

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(targetEntity:UserInfos::class, inversedBy: 'user', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "user_infos_id", referencedColumnName: "id")]
    private ?UserInfos $UserInfos = null;

    /**
     * @var Collection<int, Annonce>
     */
    #[ORM\OneToMany(targetEntity: Annonce::class, mappedBy: 'chauffeur')]
    private Collection $annonces;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'roles' => $this->roles,
            'password' => $this->password,
            'siren' => $this->siren,
            'payment' => $this->payment,
            'fileName' => $this->fileName,
            'fileSize' => $this->fileSize,
            'updatedAt' => $this->updatedAt,
            'UserInfos' => $this->UserInfos,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->roles = $data['roles'] ?? [];
        $this->password = $data['password'] ?? null;
        $this->siren = $data['siren'] ?? null;
        $this->payment = $data['payment'] ?? null;
        $this->fileName = $data['fileName'] ?? null;
        $this->fileSize = $data['fileSize'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
        $this->UserInfos = $data['UserInfos'] ?? null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantis que chaque utilisateur a au moins un rôle
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(string $siren): static
    {
        $this->siren = $siren;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): static
    {
        $this->payment = $payment;

        // ??
        $newUser = $payment === null ? null : $this;
        if ($newUser !== $payment->getUser()) {
            $payment->setUser($newUser);
        }

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

    public function setFileSize(?int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function getUserInfos(): ?UserInfos
    {
        return $this->UserInfos;
    }

    public function setUserInfos(?UserInfos $UserInfos): static
    {
        $this->UserInfos = $UserInfos;

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
            $annonce->setChauffeur($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): static
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getChauffeur() === $this) {
                $annonce->setChauffeur(null);
            }
        }

        return $this;
    }
}
