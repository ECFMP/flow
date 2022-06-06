<?php

namespace App\Imports;

use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterSheet;

class EventParticipantsImport implements ToCollection, WithValidation, SkipsEmptyRows, WithEvents
{
    use Importable;
    use RegistersEventListeners;

    public function __construct(public Event $event, public string $filePath)
    {
        //
    }

    public function collection(Collection $collection)
    {
        // If there are already participants, do we want to merge, replace or give user option?
        $this->event->update([
            'participants' => $collection->flatten(),
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => ['required']
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        Storage::disk('imports')->delete($event->getConcernable()->filePath);
    }
}
