<?php

namespace App\Service;

use App\Entity\Outing;
use App\Entity\Participant;
use App\Enum\StatusName;

class OutingAuthorizationService
{
    public function isUserOrganizer(Outing $outing, Participant $user) : bool {
        return $user->getId() === $outing->getOrganizer()->getId();
    }

    public function isUserParticipant(Outing $outing, Participant $user): bool {
        return $outing->getParticipants()->contains($user);
    }

    public function isStatusOpened(Outing $outing) : bool {
        return $outing->getStatus()->getLabel() === StatusName::OPENED;
    }

    public function isStatusCreated(Outing $outing) : bool {
        return $outing->getStatus()->getLabel() === StatusName::CREATED;
    }

    public function canUserDisplay(Outing $outing, Participant $user) : bool {
        return  $this->isUserOrganizer($outing, $user)
            && $this->isStatusOpened($outing);
    }

    public function canUserUpdate(Outing $outing, Participant $user) : bool {
        return $this->isUserOrganizer($outing, $user)
            && $this->isStatusCreated($outing);
    }

    public function canUserCancel(Outing $outing, Participant $user) : bool {
        return $this->isUserOrganizer($outing, $user)
            && ($this->isStatusCreated($outing) || $this->isStatusOpened($outing));
    }

    public function canUserRegister(Outing $outing, Participant $user) : bool {
        //Ne peut pas s'inscrire si est organisateur
        if ($this->isUserOrganizer($outing, $user)) {
            return false;
        }

        return $this->isStatusOpened($outing)
            && !$this->isUserParticipant($outing, $user)
            && $outing->getParticipants()->count() < $outing->getMaxParticipants();
    }

    public function canUserUnregister(Outing $outing, Participant $user) : bool {
        if ($this->isUserOrganizer($outing, $user)) {
            return false;
        }

        $today = new \DateTimeImmutable('today'); // date + heure à 0h00
        $outingDate = $outing->getStartingDateTime()->setTime(0, 0);

        // date dujour > date limite
        return $this->isStatusOpened($outing)
            && $outing->getParticipants()->contains($user)
            && $outingDate < $today;
    }

    public function canUserPublish(Outing $outing, Participant $user) : bool {
        return $this->isUserOrganizer($outing, $user)
            && $this->isStatusCreated($outing);
    }

}
