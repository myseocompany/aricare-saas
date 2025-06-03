<!-- resources/views/filament/resources/rips-reporting-center-resource/pages/generate-rips-report.blade.php -->
<x-filament::page>
    <x-filament::card>
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Generador de RIPS
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Seleccione el convenio y rango de fechas para generar el archivo RIPS
            </p>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            {{ $this->form }}
            
            <!--<div class="flex justify-end mt-4">
                {{ $this->generateAction }}
            </div>-->
        </div>
        
        @if($showPreview)
            <div class="px-4 py-5 sm:p-6 border-t border-gray-200 bg-gray-50">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-medium text-gray-700">Vista previa del reporte</h4>
                    <x-filament::button
                        tag="a"
                        href="{{ $downloadUrl }}"
                        color="success"
                        icon="heroicon-o-download"
                        size="sm">
                        Descargar JSON completo
                    </x-filament::button>
                </div>
                
                <div class="space-y-4">
                    <div class="p-4 bg-white border rounded">
                        <h5 class="font-medium text-sm mb-2">Estructura básica:</h5>
                        <pre class="text-xs overflow-auto max-h-40">{{ json_encode(['numFactura' => $ripsData[0]['rips']['numFactura'] ?? '', 'total_usuarios' => count($ripsData[0]['rips']['usuarios'] ?? 0)], JSON_PRETTY_UNICODE) }}</pre>
                    </div>
                    
                    <div class="p-4 bg-white border rounded">
                        <h5 class="font-medium text-sm mb-2">Primer usuario:</h5>
                        <pre class="text-xs overflow-auto max-h-60">{{ json_encode($ripsData[0]['rips']['usuarios'][0] ?? [], JSON_PRETTY_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        @endif
    </x-filament::card>
</x-filament::page>