<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Models\Event;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\EventResource;
use App\Imports\EventParticipantsImport;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class ImportParticipants extends Page implements HasForms
{
    protected static string $resource = EventResource::class;

    protected static string $view = 'filament.resources.event-resource.pages.import-participants';

    public Event $event;

    public function mount(Event $record): void
    {
        abort_unless(auth()->user()->can('update', $record), 403);
        $this->event = $record;
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('file')
                ->helperText(__("CSV file, containing only Vatsim ID's. Please note that this **overwrites** any previous imports."))
                ->disk('imports')
                ->acceptedFileTypes(['text/csv', 'text/plain'])
                ->required()
        ];
    }

    protected function getFormModel(): Model|string|null
    {
        return $this->event;
    }

    public function submit(): void
    {
        activity()
            ->by(auth()->user())
            ->on($this->event)
            ->event('Participants import')
            ->log('User has started participants import.');

        $filePath = $this->form->getState()['file'];

        (new EventParticipantsImport($this->event, $filePath))->import($filePath, 'imports');

        $this->notify('success', 'Participants imported', true);
        $this->redirectRoute('filament.resources.events.index');
    }
}
