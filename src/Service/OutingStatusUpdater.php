<?php

namespace App\Service;

use App\Entity\Outing;
use App\Enum\StatusName;
use App\Repository\StatusRepository;

class OutingStatusUpdater
{

    public function __construct(
        private StatusRepository $statusRepository
    ){}

    public function updateStatus(Outing $outing) : void
    {
        $currentLabel = $outing->getStatus()->getLabel();

        if ($currentLabel !== StatusName::OPENED) {
            return;
        }

        $today = new \DateTimeImmutable('today'); // date + heure à 0h00
        $outingDate = $outing->getStartingDateTime()->setTime(0, 0);
        $registrationDeadline = $outing->getRegistrationDeadline()->setTime(23,59,59);

        $participantsNb = $outing->getParticipants()->count();
        $maxParticipants = $outing->getMaxParticipants();

        // autre condition: si désistement
        $mustBeClosed = $participantsNb >= $maxParticipants || $today > $registrationDeadline;

        if ($outingDate < $today) {
            $newLabel = $this->statusRepository->findOneBy(['label' => StatusName::PAST]);

        } else if ($outingDate == $today) {
            $newLabel = $this->statusRepository->findOneBy(['label' => StatusName::ONGOING]);

        } else if ($mustBeClosed) {
            $newLabel = $this->statusRepository->findOneBy(['label' => StatusName::CLOSED]);

        } else {
            $newLabel = $this->statusRepository->findOneBy(['label' => StatusName::OPENED]);
        }

        $outing->setStatus($newLabel);
    }

    public function archiveOuting(Outing $outing) : void
    {
        $label = $outing->getStatus()->getLabel();

        if (!in_array($label, [StatusName::PAST, StatusName::CANCELLED], true)) {
            throw new \LogicException("Only 'past' or 'cancelled' outings can be archived, current status is '$label'.");
        }

        $today = new \DateTimeImmutable('today'); // date + heure à 0h00
        $outingDate = $outing->getStartingDateTime()->setTime(0, 0);
        $archiveDate = $outingDate->modify('+1 month'); // + 31 jours

        if ($today >= $archiveDate) {
            $status = $this->statusRepository->findOneBy(['label' => StatusName::ARCHIVED]);
        }

        $outing->setStatus($status);

    }
}
