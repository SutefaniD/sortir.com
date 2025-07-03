<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, nullable: false)]
    #[Assert\NotBlank(message: "Le nom du site est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 150,
        maxMessage: "Le nom du site ne doit pas avoir moins de 3 lettres et dépasser {{ limit }} caractères."
    )]
    private string $name;

    /**
     * @var Collection<int, Outing>
     */
    #[ORM\OneToMany(targetEntity: Outing::class, mappedBy: 'site')]
    private Collection $outings;

    public function __construct()
    {
        $this->outings = new ArrayCollection();
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

    /**
     * @return Collection<int, Outing>
     */
    public function getOutings(): Collection
    {
        return $this->outings;
    }

    public function addOuting(Outing $outing): static
    {
        if (!$this->outings->contains($outing)) {
            $this->outings->add($outing);
            $outing->setSite($this);
        }

        return $this;
    }

    public function removeOuting(Outing $outing): static
    {
        if ($this->outings->removeElement($outing)) {
            // set the owning side to null (unless already changed)
            if ($outing->getSite() === $this) {
                $outing->setSite(null);
            }
        }

        return $this;
    }
}
