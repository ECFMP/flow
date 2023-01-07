<x-filament::page>
    <div style="max-width: 25%">
        @livewire('airport-search')
    </div>
    @if ($airport && $airport['latitude'] && $airport['longitude'])
    @livewire('airport-overview', ['airportId' => $airport['id']])
    @livewire('airport-graph', ['airportId' => $airport['id']])
    @livewire('airport-inbounds', ['airportId' => $airport['id']])
    @elseif ($airport)
    <div>Cannot display arrival information for this airport. Please ensure that the airports latitude and longitude are
        configured.</div>
    @endif
</x-filament::page>
