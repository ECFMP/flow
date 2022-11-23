<x-filament::page>
    <div class="space-y-2">
        <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse"
            for="airportId"><span
                class="text-sm font-medium leading-4 text-gray-700 dark:text-gray-300">Airport</span></label>
        <select wire:model="airportId"
            class="block w-full max-w-xs h-9 pl-9 placeholder-gray-400 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
            id="airportId">
            <option value="0">---</option>
            @foreach ($airports as $id => $icao)
            <option value={{$id}}>{{$icao}}</option>
            @endforeach
        </select>
    </div>
    @if ($airport && $airport['latitude'] && $airport['longitude'])
        @livewire('airport-overview', ['airportId' => $airport['id']])
        @livewire('airport-inbounds', ['airportId' => $airport['id']])
    @elseif ($airport)
    <div>Cannot display arrival information for this airport. Please ensure that the airports latitude and longitude are
        configured.</div>
    @endif
</x-filament::page>
