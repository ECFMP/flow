<?php

namespace App\Vatsim\Processor;

interface VatsimDataProcessorInterface
{
    /**
     * Function for processing network data. 
     */
    public function processNetworkData(array $data): void;
}
