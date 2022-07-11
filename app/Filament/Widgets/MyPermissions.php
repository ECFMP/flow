<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class MyPermissions extends Widget
{
    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.my-permissions';

    protected function getViewData(): array
    {
        return [
            'user' => auth()->user(),
        ];
    }
}
