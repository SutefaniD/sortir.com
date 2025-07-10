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

    public function isUserAdmin(Participant $user): bool {
        return $user->isAdministrator();
    }

    public function isStatusCreated(Outing $outing) : bool {
        return $outing->getStatus()->getLabel() === StatusName::CREATED;
    }
    public function isStatusOpened(Outing $outing) : bool {
        return $outing->getStatus()->getLabel() === StatusName::OPENED;
    }

    public function isStatusClosed(Outing $outing) : bool {
        return $outing->getStatus()->getLabel() === StatusName::CLOSED;
    }

    public function isStatusOngoing(Outing $outing) : bool {
        return $outing->getStatus()->getLabel() === StatusName::ONGOING;
    }

    public function isStatusPast(Outing $outing) : bool {
        return $outing->getStatus()->getLabel() === StatusName::PAST;
    }

    public function isStatusCancelled(Outing $outing) : bool {
        return $outing->getStatus()->getLabel() === StatusName::CANCELLED;
    }

    public function isStatusArchived(Outing $outing) : bool {
        return $outing->getStatus()->getLabel() === StatusName::ARCHIVED;
    }

    /**
     * Display outing if
     * * *  - status is opened or closed for everyone
     * * *  - status is ongoing for participant (and organizer ?)
     * @param Outing $outing
     * @param Participant $user
     * @return bool
     */
    public function canUserDisplay(Outing $outing, Participant $user) : bool {
        return  $this->isStatusOpened($outing)
            || $this->isStatusClosed($outing)
            || ($this->isStatusOngoing($outing) && $this->isUserParticipant($outing, $user));
    }

    /**
     * Update outing possible only for status created and by organizer
     * @param Outing $outing
     * @param Participant $user
     * @return bool
     */
    public function canUserUpdate(Outing $outing, Participant $user) : bool {
        return $this->isUserOrganizer($outing, $user)
            && $this->isStatusCreated($outing);
    }

    /**
     * Only organizer can cancel an outing with status CREATED or OPENED.
     * @param Outing $outing
     * @param Participant $user
     * @return bool
     */
    public function canUserCancel(Outing $outing, Participant $user) : bool {
        return $this->isUserOrganizer($outing, $user)
            && ($this->isStatusCreated($outing) || $this->isStatusOpened($outing));
    }

    /**
     * Only the organizer can publish an outing if its status is "Created".
     * (publish means set the status on "Opened")
     * @param Outing $outing
     * @param Participant $user
     * @return bool
     */
    public function canUserPublish(Outing $outing, Participant $user) : bool {
        return $this->isUserOrganizer($outing, $user)
            && $this->isStatusCreated($outing);
    }

    /**
     * Users can register for an outing only if
     *  - they are not already registered,
     *  - are not the organizer,
     *  - the outing is open,
     *  - and the maximum number of participants has not been reached.
     * @param Outing $outing
     * @param Participant $user
     * @return bool
     */
    public function canUserRegister(Outing $outing, Participant $user) : bool {
        //Ne peut pas s'inscrire si est organisateur
        if ($this->isUserOrganizer($outing, $user)) {
            return false;
        }

        return $this->isStatusOpened($outing)
            && !$this->isUserParticipant($outing, $user)
            && $outing->getParticipants()->count() < $outing->getMaxParticipants();
    }

    /**
     * Users can unregister for an outing only if
     *   - they are already registered,
     *   - are not the organizer,
     *   - and the outing is not ongoing or past.
     * @param Outing $outing
     * @param Participant $user
     * @return bool
     */
    public function canUserUnregister(Outing $outing, Participant $user) : bool {
        if ($this->isUserOrganizer($outing, $user)) {
            return false;
        }

        $today = new \DateTimeImmutable('today'); // date + heure à 0h00
        $outingDate = $outing->getStartingDateTime()->setTime(0, 0);

        // date dujour > date limite
        return ($this->isStatusOpened($outing) || $this->isStatusClosed($outing))
            && $outing->getParticipants()->contains($user)
            && $today < $outingDate;
    }

}
