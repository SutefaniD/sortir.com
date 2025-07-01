<?php

namespace App\Entity;

    use App\Repository\ParticipantRepository;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\ORM\Mapping as ORM;
    use phpDocumentor\Reflection\Types\Boolean;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $lastName = null;

    #[ORM\Column(length: 150)]
    private ?string $firstName = null;

    #[ORM\Column]
    private ?int $phone = null;

    #[ORM\Column(length: 150)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $administrator = false;

    #[ORM\Column(type: 'boolean')]
    private ?bool $active = false;


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

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
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

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): static
    {
        $this->phone = $phone;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getAdministrator(): ?bool
    {
        return $this->administrator;
    }

    public function setAdministrator(?bool $administrator): void
    {
        $this->administrator = $administrator;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): void
    {
        $this->active = $active;
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
}
