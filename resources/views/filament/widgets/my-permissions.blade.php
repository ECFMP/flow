<x-filament::widget>
    <x-filament::card>
        <x-tables::header.heading>
            {{ __('My permissions') }}
        </x-tables::header.heading>

        <x-tables::hr />

        <x-tables::table>
            <div class="prose dark:prose-invert">
                <p>
                    <strong>Role</strong>: {{ $user->role->description }}
                </p>
                @if ($user->role->key != App\Enums\RoleKey::USER)
                    <p><strong>Flight Information Regions:</strong>
                        @if (in_array($user->role->key, [App\Enums\RoleKey::SYSTEM, App\Enums\RoleKey::NMT]))
                            All
                        @else
                            <ul>
                                @forelse ($user->flightInformationRegions as $fir)
                                    <li>
                                        {{ $fir->identifierName }}
                                    </li>
                                @empty
                                    <li>None</li>
                                @endforelse
                            </ul>
                        @endif
                    </p>
                @endif
            </div>
        </x-tables::table>
    </x-filament::card>
</x-filament::widget>
