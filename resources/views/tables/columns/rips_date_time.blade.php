@if ($getRecord()->created_at === null)
    N/A
@else
    <x-filament::badge>
    <div>{{ \Carbon\Carbon::parse($getRecord()->service_datetime)->format('h:i A') }}
        <div>
            {{ \Carbon\Carbon::parse($getRecord()->service_datetime)->translatedFormat('jS M, Y') }}
        </div>
    </div>
    </x-filament::badge>
@endif
