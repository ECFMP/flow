<?php

namespace App\Imports;

use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
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

    public function __construct(public Event $event, public string $filePath)
    {
        //
    }

    public function collection(Collection $rows)
    {
        $cids = $rows
            ->flatten()
            ->filter(fn ($row) => $this->hasValidCid($row))
            ->values();

        $this->event->update([
            'participants' => $cids,
        ]);
    }

    private function hasValidCid($cid)
    {
        $cid = (int) $cid;
        return $cid >= self::MINIMUM_MEMBER_CID ||
            ($cid >= self::MINIMUM_CID && $cid <= self::MAXIMUM_FOUNDER_CID);
    }

    public static function afterSheet(AfterSheet $event)
    {
        Storage::disk('imports')->delete($event->getConcernable()->filePath);
    }
}
