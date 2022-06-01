<?php

namespace App\Imports;

use App\Models\Airport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithUpserts;

class AirportImport implements ToModel, WithBatchInserts, WithUpserts
{
    use Importable;

    private function rowValid(array $row): bool
    {
        return count($row) === 1 &&
            is_string($row[0]) &&
            strlen($row[0]) === 4;
    }

    public function model(array $row)
    {
        if (!$this->rowValid($row)) {
            $this->output->warning(sprintf('Invalid row: %s', json_encode($row)));
            return null;
        }

        return new Airport(['icao_code' => $row[0]]);
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function uniqueBy()
    {
        return 'icao_code';
    }
}
