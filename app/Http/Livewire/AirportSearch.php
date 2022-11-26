<?php

namespace App\Http\Livewire;

use App\Models\Airport;
use Livewire\Component;

class AirportSearch extends Component
{
    public $query;
    public $airports;
    public $highlightIndex;

    public $selectedAirport;

    public function mount()
    {
        $this->resetSearch();
    }

    public function resetSearch()
    {
        $this->query = '';
        $this->airports = [];
        $this->highlightIndex = 0;
    }

    public function incrementHighlight()
    {
        if ($this->highlightIndex === count($this->airports) - 1) {
            $this->highlightIndex = 0;
            return;
        }

        $this->highlightIndex++;
    }

    public function decrementHighlight()
    {
        if ($this->highlightIndex === 0) {
            $this->highlightIndex = count($this->airports) - 1;
            return;
        }

        $this->highlightIndex--;
    }

    public function setHighlight($highlightIndex)
    {
        $this->highlightIndex = $highlightIndex;
    }

    public function selectAirport()
    {
        $airportId = $this->airports[$this->highlightIndex]['id'];
        if ($airportId) {
            $this->selectedAirport = Airport::findOrFail($airportId);
            $this->emit('airportIdUpdated', $airportId);
            $this->resetSearch();
        }
    }

    public function updatedQuery()
    {
        $this->airports = Airport::where('icao_code', 'like', $this->query . '%')
            ->orderBy('icao_code')
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.airport-search');
    }
}
