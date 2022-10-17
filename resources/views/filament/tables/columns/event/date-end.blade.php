<div>
    @if ($getRecord()->date_start->isSameDay($getRecord()->date_end))
        {{ $getState()->format('H:i\z') }}
    @else
        {{ $getState()->format('M j, Y') }} <strong>{{ $getState()->format('H:i\z') }}</strong>
    @endif
</div>
