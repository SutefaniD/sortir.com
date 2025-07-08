<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('email', message: 'Cet email est déjà utilisé.')]
#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Please enter your last name')]
    #[Assert\Length(max: 150, maxMessage: 'Too long ! 150 characters at most !')]
    #[ORM\Column(length: 150, nullable: false)]
    private ?string $lastName = null;

    #[Assert\NotBlank(message: 'Please enter your first name')]
    #[Assert\Length(max: 150, maxMessage: 'Too long ! 150 characters at most !')]
    #[ORM\Column(length: 150, nullable: false)]
    private ?string $firstName = null;

    #[Assert\NotBlank(message: 'Please enter your username')]
    #[Assert\Length(max: 150, maxMessage: 'Too long ! 150 characters at most !')]
    #[ORM\Column(length: 150, nullable: false, unique: true)]
    private ?string $username = null;

    #[Assert\NotBlank(message: 'Please enter your phone number')]
    #[Assert\Length(min: 10, maxMessage: 'Too short ! 10 characters at least !')]
    #[Assert\Length(max: 10, maxMessage: 'Too long ! 10 characters at most !')]
    #[ORM\Column(length: 10, nullable: false)]
    private ?string $phone = null;

    #[Assert\NotBlank(message: 'Please enter your email')]
    #[Assert\Length(max: 150, maxMessage: 'Too long ! 150 characters at most !')]
    #[ORM\Column(length: 150, nullable: false)]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'Please enter your password')]
    #[Assert\Length(max: 255, maxMessage: 'Too long ! 255 characters at most !')]
    #[ORM\Column(length: 255, nullable: false)]
    private ?string $password = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $administrator = false;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $active = false;

    #[ORM\Column(nullable: true)]
    private ?string $profilePicture = null;

    #[Assert\File(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        mimeTypesMessage: 'Merci de télécharger une image valide (jpg, png, webp).'
    )]
    private ?UploadedFile $profileImageFile = null;

    /**
     * @var Collection<int, Outing>
     */
    #[ORM\ManyToMany(targetEntity: Outing::class, mappedBy: 'participants')]
    private Collection $registeredOutings;

    /**
     * @var Collection<int, Outing>
     */
    #[ORM\OneToMany(targetEntity: Outing::class, mappedBy: 'organizer')]
    private Collection $organizedOutings;

    #[ORM\ManyToOne(targetEntity: Site::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    public function __construct()
    {
        $this->registeredOutings = new ArrayCollection();
        $this->organizedOutings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isAdministrator(): bool
    {
        return $this->administrator;
    }

    public function setAdministrator(bool $administrator): static
    {
        $this->administrator = $administrator;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    public function getProfileImageFile(): ?UploadedFile
    {
        return $this->profileImageFile;
    }

    public function setProfileImageFile(?UploadedFile $profileImageFile): void
    {
        $this->profileImageFile = $profileImageFile;
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->administrator) {
            $roles[] = 'ROLE_ADMIN';
        }

        return $roles;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Outing>
     */
    public function getRegisteredOutings(): Collection
    {
        return $this->registeredOutings;
    }

    public function addRegisteredOuting(Outing $registeredOuting): static
    {
        if (!$this->registeredOutings->contains($registeredOuting)) {
            $this->registeredOutings->add($registeredOuting);
            $registeredOuting->addParticipant($this);
        }

        return $this;
    }

    public function removeRegisteredOuting(Outing $registeredOuting): static
    {
        if ($this->registeredOutings->removeElement($registeredOuting)) {
            $registeredOuting->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Outing>
     */
    public function getOrganizedOutings(): Collection
    {
        return $this->organizedOutings;
    }

    public function addOrganizedOuting(Outing $organizedOuting): static
    {
        if (!$this->organizedOutings->contains($organizedOuting)) {
            $this->organizedOutings->add($organizedOuting);
            $organizedOuting->setOrganizer($this);
        }

        return $this;
    }

    public function removeOrganizedOuting(Outing $organizedOuting): static
    {
        if ($this->organizedOutings->removeElement($organizedOuting)) {
            // set the owning side to null (unless already changed)
            if ($organizedOuting->getOrganizer() === $this) {
                $organizedOuting->setOrganizer(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }
}
