<?php

namespace App\Enums;

enum RoleKey: string
{
    case SYSTEM = 'SYSTEM';
    case NMT = 'NMT';
    case FLOW_MANAGER = 'FLOW_MANAGER';
    case EVENT_MANAGER = 'EVENT_MANAGER';
    case USER = 'USER';
}
