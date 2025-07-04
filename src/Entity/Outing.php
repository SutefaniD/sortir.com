<?php

namespace App\Entity;

    use App\Repository\OutingRepository;
    use DateTime;
    use DateTimeInterface;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\DBAL\Types\Types;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;

    #[ORM\Entity(repositoryClass: OutingRepository::class)]
class Outing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Le nom de la sortie est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private string $name;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\NotNull(message: "La date et l'heure de la sortie sont obligatoires.")]
    #[Assert\GreaterThan("now", message: "La sortie doit être prévue dans le futur.")]
    private \DateTimeInterface $startingDateTime;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Assert\NotNull(message: "La durée est obligatoire.")]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: "La durée ne peut pas être négative."
    )]
    private int $duration;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\NotNull(message: "La date limite d'inscription est obligatoire.")]
    #[Assert\LessThan(propertyPath: "startingDateTime", message: "La date limite doit être avant la date de la sortie.")]
    private DateTimeInterface $registrationDeadline;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Assert\NotNull(message: "Le nombre de places est obligatoire.")]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: "Le nombre de participants ne peut pas être négatif."
    )]
    private int $maxParticipants;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Veuillez renseigner les détails de la sortie.")]
    private ?string $outingDetails = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: 'registeredOutings')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'organizedOutings')]
    #[Assert\NotNull(message: "L'organisateur est requis.")]
    private ?Participant $organizer = null;

    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: "outings")]
    #[ORM\JoinColumn(nullable: true)]
//    #[Assert\NotNull(message: "Le statut de la sortie est obligatoire.")]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le lieu est requis.")]
    private ?Location $location = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cancelReason = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le site est obligatoire.")]
    private ?Site $site = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartingDateTime(): DateTimeInterface
    {
        return $this->startingDateTime;
    }

    public function setStartingDateTime(DateTimeInterface $startingDateTime): static
    {
        $this->startingDateTime = $startingDateTime;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRegistrationDeadline(): DateTimeInterface
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(DateTimeInterface $registrationDeadline): static
    {
        $this->registrationDeadline = $registrationDeadline;

        return $this;
    }

    public function getMaxParticipants(): int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(int $maxParticipants): static
    {
        $this->maxParticipants = $maxParticipants;

        return $this;
    }

    public function getOutingDetails(): ?string
    {
        return $this->outingDetails;
    }

    public function setOutingDetails(?string $outingDetails): static
    {
        $this->outingDetails = $outingDetails;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addRegisteredOuting($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        $this->participants->removeElement($participant);
        $participant->removeRegisteredOuting($this);

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getOrganizer(): ?Participant
    {
        return $this->organizer;
    }

    public function setOrganizer(?Participant $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getCancelReason(): ?string
    {
        return $this->cancelReason;
    }

    public function setCancelReason(?string $cancelReason): void
    {
        $this->cancelReason = $cancelReason;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): void
    {
        $this->site = $site;
    }
}
