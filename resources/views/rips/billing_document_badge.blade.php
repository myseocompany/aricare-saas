<x-filament::button wire:click="mountTableAction('view', '{{ $record->id }} ')" size="xs" color="" outlined>
    <x-filament::badge color="info">
        @if (!is_null($record))
            {{ $record->billingDocument->document_number ?? 'Sin factura' }}
        @else
            Sin factura
        @endif


    </x-filament::badge>
</x-filament::button>
