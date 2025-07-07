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

    public function canUserDisplay(Outing $outing, Participant $user) : bool {
        return  $this->isUserOrganizer($outing, $user)
            && $outing->getStatus() === StatusName::OPENED;
    }

    public function canUserUpdate(Outing $outing, Participant $user) : bool {
        return $this->isUserOrganizer($outing, $user)
            && $outing->getStatus() === StatusName::CREATED;
    }

    public function canUserCancel(Outing $outing, Participant $user) : bool {
        return $this->isUserOrganizer($outing, $user)
            && ($outing->getStatus() === StatusName::CREATED
                || $outing->getStatus() === StatusName::OPENED);
    }

    public function canUserRegister(Outing $outing, Participant $user) : bool {

    }

    public function canUserUnregister() : void {

    }

}
