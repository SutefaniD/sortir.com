<?php


namespace App\Entity;

    use App\Repository\OutingRepository;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\DBAL\Types\Types;
    use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutingRepository::class)]
class Outing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTime $startingDateTime = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column]
    private ?\DateTime $registrationDeadline = null;

    #[ORM\Column]
    private ?int $maxParticipant = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $outingDetails = null;


    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: 'registeredOutings')]
    private Collection $participants;

    #[ORM\ManyToOne(targetEntity: Status::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'organizedOutings')]
    private ?Participant $organizer = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    private ?Site $site = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getStartingDateTime(): ?\DateTime
    {
        return $this->startingDateTime;
    }

    public function setStartingDateTime(\DateTime $startingDateTime): static
    {
        $this->startingDateTime = $startingDateTime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRegistrationDeadline(): ?\DateTime
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(\DateTime $registrationDeadline): static
    {
        $this->registrationDeadline = $registrationDeadline;

        return $this;
    }

    public function getMaxParticipant(): ?int
    {
        return $this->maxParticipant;
    }

    public function setMaxParticipant(int $maxParticipant): static
    {
        $this->maxParticipant = $maxParticipant;

        return $this;
    }

    public function getOutingDetails(): ?string
    {
        return $this->outingDetails;
    }

    public function setOutingDetails(string $outingDetails): static
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
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        $this->participants->removeElement($participant);

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
