<?php

namespace App\Discord\FlowMeasure\Message;

use Illuminate\Support\Collection;

interface MessageGeneratorInterface
{
    public function generate(): Collection;
}
