<div class="relative">
    <div class="space-y-2">
        <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
            <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse"
                for="airportSelection">
                <span class="text-sm font-medium leading-4 text-gray-700 dark:text-gray-300">
                    Select Airport
                </span>
            </label>
        </div>
        <div class="filament-forms-select-component flex items-center rtl:space-x-reverse group">
            <input type="search" id="airportSelection" style="width: 100%"
                class="block transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600"
                placeholder="Search Airports..." wire:model="query" wire:keydown.escape="resetSearch"
                wire:keydown.tab="resetSearch" wire:keydown.arrow-up="decrementHighlight"
                wire:keydown.arrow-down="incrementHighlight" wire:keydown.enter="selectAirport"/>

            @if(!empty($query))
            <div class="choices__list choices__list--dropdown is-active block listbox" @click.outside="$wire.resetSearch()">
                @if(!empty($airports))
                @foreach($airports as $i => $airport)
                <div wire:mouseover="setHighlight({{$i}})" , wire:click="selectAirport" style="width: 100%"
                    class="choices__item choices__item--choice choices__item--selectable {{ $highlightIndex === $i ? 'is-highlighted' : '' }}">
                    {{$airport['icao_code'] }}
                </div>
                @endforeach
                @else
                <div class="choices__item choices__item--choice choices__item--selectable">No results!</div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
