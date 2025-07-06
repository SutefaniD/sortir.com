<?php

namespace App\DTO;

use App\Entity\Site;

class SearchFormDTO
{
    private ?Site $site = null;
    public ?string $outingName = null;
    private ?\DateTimeInterface $startDate = null;
    private ?\DateTimeInterface $endDate = null;
    private ?bool $isOrganizer = false;

//
//    public ?bool isParticipant = false;
//
//    public ?bool isNotParticipant = false;

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): void
    {
        $this->site = $site;
    }

    public function getOutingName(): ?string
    {
        return $this->outingName;
    }

    public function setOutingName(?string $outingName): void
    {
        $this->outingName = $outingName;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getIsOrganizer(): ?bool
    {
        return $this->isOrganizer;
    }

    public function setIsOrganizer(?bool $isOrganizer): void
    {
        $this->isOrganizer = $isOrganizer;
    }
}
