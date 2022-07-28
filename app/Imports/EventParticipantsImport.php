<?php

namespace App\Imports;

use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class EventParticipantsImport implements ToCollection, WithEvents
{
    use Importable;
    use RegistersEventListeners;
    use SkipsFailures;

    // The minimum possible CID
    private const MINIMUM_CID = 800000;

    // The maximum realistic founder CID
    private const MAXIMUM_FOUNDER_CID = 800150;

    // The minimum "normal" CID
    private const MINIMUM_MEMBER_CID = 810000;

    private const AIRFIELD_ICAO_REGEX = '/[A-Za-z]{4}/';

    public function __construct(public Event $event, public string $filePath)
    {
        //
    }

    public function collection(Collection $rows)
    {
        $this->event->participants()->delete();
        $this->event->participants()->createMany(
            $rows
                ->filter(fn($row) => $this->rowValid($row))
                ->map(fn(Collection $row): array => [
                    'cid' => $row[0],
                    'origin' => empty($row[1]) ? null : Str::upper($row[1]),
                    'destination' => empty($row[2]) ? null : Str::upper($row[2]),
                ])
                ->values()
        );
    }

    private function rowValid(Collection $row): bool
    {
        return $this->hasValidCid($row[0]) &&
            $this->hasValidAirfield($row[1]) &&
            $this->hasValidAirfield($row[2]);
    }

    private function hasValidCid($cid): bool
    {
        if (!is_int($cid)) {
            return false;
        }

        $cid = (int)$cid;
        return $cid >= self::MINIMUM_MEMBER_CID ||
            ($cid >= self::MINIMUM_CID && $cid <= self::MAXIMUM_FOUNDER_CID);
    }

    private function hasValidAirfield($airfield): bool
    {
        return empty($airfield) || preg_match(self::AIRFIELD_ICAO_REGEX, (string)$airfield) === 1;
    }

    public static function afterSheet(AfterSheet $event)
    {
        Storage::disk('imports')->delete($event->getConcernable()->filePath);
    }
}
