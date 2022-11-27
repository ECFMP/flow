<?php

namespace App\Http\Livewire;

use App\Models\Airport;
use Livewire\Component;

class AirportSearch extends Component
{
    public $query;
    public $airports;
    public $selectedAirport;

    public function mount()
    {
        $this->resetSearch();
    }

    public function resetSearch()
    {
        $this->query = '';
        $this->airports = [];
    }

    public function selectAirport(int $index)
    {
        $airportId = $this->airports[$index]['id'];
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
