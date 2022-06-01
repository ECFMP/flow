<?php

namespace App\Imports;

use App\Models\Airport;
use App\Models\AirportGroup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class AirportGroupImport implements ToCollection
{
    use Importable;

    private function rowValid(Collection $row): bool
    {
        return count($row) >= 2;
    }

    public function collection(Collection $rows)
    {
        $rows->each(function (Collection $row) {
            if (!$this->rowValid($row)) {
                $this->output->warning(sprintf('Invalid row: %s', json_encode($row)));
                return null;
            }

            $group = AirportGroup::firstOrCreate(['name' => $row[0]]);
            $airports = array_map(
                fn(string $airport) => trim($airport),
                explode(',', $row[1])
            );

            $group->airports()->sync(
                Airport::whereIn('icao_code', $airports)->pluck('id')->toArray()
            );
        });
    }
}
