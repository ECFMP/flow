<?php

namespace App\Models;

enum VatsimPilotStatus: int
{
    case Ground = 1;
    case Departing = 2;
    case Cruise = 3;

    case Descending = 4;

    case Landed = 5;
}
