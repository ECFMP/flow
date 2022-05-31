<?php

namespace App\Filament\Resources;

use App\Enums\RoleKey;
use Spatie\Activitylog\Models\Activity;
use Z3d0X\FilamentLogger\Resources\ActivityResource as ResourcesActivityResource;

class ActivityResource extends ResourcesActivityResource
{
    protected static function getNavigationGroup(): ?string
    {
        return 'Admin';
    }
}
