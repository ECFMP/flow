<x-filament::page>
    @livewire('airport-overview', ['airportId' => $airportId])
    @livewire('airport-inbounds', ['airportId' => $airportId])
</x-filament::page>
