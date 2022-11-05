@if (config('app.env') === 'staging')
    <div style="width: 100%; background-color: red; color:white" class="font-bold tracking-tight text-center bg-danger-50/50">
        This is the ECFMP dev site. Please do not add real data here.
    </div>
@endif