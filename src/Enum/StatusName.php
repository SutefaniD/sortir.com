<?php

namespace App\Enum;


enum StatusName: string
{
    case CREATED = 'created';
    case ONGOING = 'ongoing';
    case PAST = 'past';
    case CANCELLED = 'cancelled';
}
