<?php

namespace App\Discord\FlowMeasure\Provider;

use Illuminate\Support\Collection;

interface MessageProviderInterface
{
    public function pendingMessages(): Collection;
}
