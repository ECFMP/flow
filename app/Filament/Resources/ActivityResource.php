<?php

namespace App\Filament\Resources;

use Z3d0X\FilamentLogger\Resources\ActivityResource as ResourcesActivityResource;

class ActivityResource extends ResourcesActivityResource
{
    protected static function getNavigationGroup(): ?string
    {
        return 'Admin';
    }
}
