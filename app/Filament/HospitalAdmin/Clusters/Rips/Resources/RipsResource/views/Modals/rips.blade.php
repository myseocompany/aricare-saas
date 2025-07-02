{{-- resources/views/modals/rips.blade.php --}}
<x-filament::modal id="ripsModal" align="center">
    <x-slot name="title">
        Archivos RIPS generados
    </x-slot>

    <x-slot name="content">
        @if(session()->has('rips_download_links'))
            <ul>
                @foreach(session('rips_download_links') as $file)
                    <li><a href="{{ $file }}" target="_blank">Descargar archivo</a></li>
                @endforeach
            </ul>
        @else
            <p>No se generaron archivos RIPS para los registros seleccionados.</p>
        @endif
    </x-slot>

    <x-slot name="footer">
        <x-filament::button
            color="secondary"
            wire:click="$emit('closeModal')"
        >
            Cerrar
        </x-filament::button>
    </x-slot>
</x-filament::modal>
