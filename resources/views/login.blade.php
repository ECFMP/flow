<div @class([
    'flex items-center justify-center min-h-screen filament-login-page bg-gray-100 text-gray-900',
    'dark:bg-gray-900 dark:text-white' => config('filament.dark_mode'),
])>
    <div class="w-screen max-w-md px-6 -mt-16 space-y-8 md:mt-0 md:px-2">
        <form wire:submit.prevent="authenticate" @class([
            'p-8 space-y-8 bg-white/50 backdrop-blur-xl border border-gray-200 shadow-2xl rounded-2xl relative',
            'dark:bg-gray-900/50 dark:border-gray-700' => config('filament.dark_mode'),
        ])>
            <div class="flex justify-center w-full">
                <x-filament::brand />
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-center">
                {{ __('filament::login.heading') }}
            </h2>
            @if (config('app.env') === 'staging')
                <h3 class="font-bold tracking-tight text-center bg-danger-50/50" style="color: red">
                    This is the dev site. Do not add real data here.
                </h3>
            @endif

            <x-filament::button type="submit" form="authenticate" class="w-full">
                Login via VATSIM Connect
            </x-filament::button>
        </form>
    </div>

    <x-filament::notification-manager />
</div>
