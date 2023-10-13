<?php

namespace App\Discord\FlowMeasure\Provider;
use Illuminate\Support\Collection;

class EcfmpProvider implements MessageProviderInterface
{
    public function __construct()

    public function pendingMessages(): Collection
    {

        return new Collection();
    }
}
