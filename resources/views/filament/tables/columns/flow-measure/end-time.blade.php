<div>
    @if ($getRecord()->start_time->isSameDay($getRecord()->end_time))
        {{ $getState()->format('H:i\z') }}
    @else
        {{ $getState()->format('M j, Y') }} <strong>{{ $getState()->format('H:i\z') }}</strong>
    @endif
</div>
