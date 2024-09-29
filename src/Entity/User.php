<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Entity\Traits\DateTimeTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])] // pour ne pas avoir plusieurs fois le même email en BDD
#[UniqueEntity(fields: ['siren'], message: 'Informations déjà existantes')] // assure que çe champ reste unique en bdd
#[ORM\HasLifecycleCallbacks] 
#[Uploadable] // indique que cette entité est "uploadable"
class User implements UserInterface, PasswordAuthenticatedUserInterface 
{
    use DateTimeTrait; 

    #[ORM\Id]
    #[ORM\GeneratedValue] // génère automatiquement l'id en bdd
    #[ORM\Column] // correspond à une colonne en bdd
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)] 
    #[Assert\NotBlank()] // empêche la soumission du form vide
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank()] // empêche la soumission du form vide
    private ?string $lastName = null;

    #[ORM\Column(length: 180)] // colonne de max 180 caractères
    #[Assert\Length(max: 180)] // chaine 180 caractères max
    #[Assert\NotBlank(message: 'L\'adresse email ne peut pas être vide.')] 
    #[Assert\Email(message: 'Email invalide.')] // oblige l'utilisateur à mettre un email valide
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
    private ?string $password = null;

    #[ORM\Column(length: 9)]
    #[Assert\NotBlank(message: 'Le numéro de Siren ne peut pas être vide.')]
    #[Assert\Regex(
        pattern: '/^\d{9}$/',
        message: 'Le numéro de Siren doit être composé de 9 chiffres.'
    )]
    private ?string $siren = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])] // pareil qu'en haut
    private ?Payment $payment = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(targetEntity:UserInfos::class, inversedBy: 'user', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "user_infos_id", referencedColumnName: "id")]
    private ?UserInfos $UserInfos = null;

    /**
     * @var Collection<int, Annonce>
     */
    #[ORM\OneToMany(targetEntity: Annonce::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $annonces;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'creator')]
    private Collection $conversations;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messages;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
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
            $annonce->setUser($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): static
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getUser() === $this) {
                $annonce->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): static
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations->add($conversation);
            $conversation->setCreator($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): static
    {
        if ($this->conversations->removeElement($conversation)) {
            // set the owning side to null (unless already changed)
            if ($conversation->getCreator() === $this) {
                $conversation->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }

        return $this;
    }
}
