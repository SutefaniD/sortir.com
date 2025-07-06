<?php

namespace App\Enum;


enum StatusName: string
{
    case CREATED = 'Créée';
    case OPENED = "Ouverte";
    case CLOSED = "Clôturée";
    case ONGOING = 'Activité en cours';
    case PAST = 'Passée';
    case CANCELLED = 'Annulée';
}
