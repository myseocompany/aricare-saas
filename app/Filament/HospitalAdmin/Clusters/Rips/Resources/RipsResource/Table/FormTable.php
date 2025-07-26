<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Table;

use Illuminate\Contracts\View\View;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;


use App\Models\Rips\RipsTenantPayerAgreement;
use App\Models\Patient;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use App\Models\Rips\RipsPatientService;

use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource;


use App\Services\RipsGeneratorService;

class FormTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->query(fn () => RipsPatientService::with(['billingDocument', 'patient.user', 'doctor.user'])
                ->orderByDesc('service_datetime'))

            ->columns([
            TextColumn::make('billingDocument.document_number')
                ->label('Factura')
                ->searchable()
                ->color('info')
                ->sortable()
                ->formatStateUsing(function ($record): ?View {
                        if (!$record->billingDocument) {
                            return view('rips.billing_document_badge', ['record' => null]);

                        }

                        return view('rips.billing_document_badge', ['record' => $record]);
                }),

            Tables\Columns\TextColumn::make('billingDocument.submission_status')
                ->label('Estado')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'accepted' => 'success',
                    'rejected' => 'danger',
                    'pending'  => 'primary',
                    default    => 'gray',
                })
                ->formatStateUsing(fn ($state) => match ($state) {
                    'accepted' => 'Aceptado',
                    'rejected' => 'Rechazado',
                    'pending'  => 'Pendiente',
                    default    => 'Sin estado',
                })
                ->sortable(),

            Tables\Columns\SpatieMediaLibraryImageColumn::make('patient.user.profile')
                ->label('Paciente')
                ->circular()
                ->defaultImageUrl(fn($record) =>
                    !$record->patient->user->hasMedia('profile')
                        ? getUserImageInitial($record->patient->id, $record->patient->user->full_name)
                        : null
                )
                ->collection('profile')
                ->width(40)
                ->height(40)
                ->url(fn($record) => \App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource::getUrl('view', ['record' => $record->patient->id]))

                ->tooltip(fn($record) => $record->patient->user->email)
                ->extraAttributes(['class' => 'mr-2']),

            TextColumn::make('patient.patientUser.full_name')
                ->label('')
                ->html()
                ->formatStateUsing(fn($record) => '
                    <div class="flex flex-col leading-tight">
                        <a href="' . \App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource::getUrl('view', ['record' => $record->patient->id]) . '" 
                        class="text-sm font-semibold text-custom-600 dark:text-custom-400 hover:underline hoverLink transition" style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                            ' . e($record->patient->patientUser->full_name) . '
                        </a>
                        <span class="text-xs text-gray-400">' . e($record->patient->patientUser->email ?? __('messages.common.n/a')) . '</span>
                    </div>
                ')
                ->searchable(['users.first_name', 'users.last_name']),


            Tables\Columns\SpatieMediaLibraryImageColumn::make('doctor.user.profile')
                ->label('Médico')
                ->circular()
                ->defaultImageUrl(fn($record) =>
                    !$record->doctor->user->hasMedia('profile')
                        ? getUserImageInitial($record->doctor->id, $record->doctor->user->full_name)
                        : null
                )
                ->collection('profile')
                ->width(40)
                ->height(40)
                ->url(fn($record) => DoctorResource::getUrl('view', ['record' => $record->doctor->id]))

                ->tooltip(fn($record) => $record->doctor->user->email)
                ->extraAttributes(['class' => 'mr-2']),

            Tables\Columns\TextColumn::make('doctor.user.full_name')
                ->label('')
                ->html()
                ->formatStateUsing(fn($record) => '
                    <div class="flex flex-col leading-tight">
                        <a href="' . DoctorResource::getUrl('view', ['record' => $record->doctor->id]) . '" 
                        class="font-semibold text-sm text-custom-600 dark:text-custom-400 hover:underline transition" style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                            ' . e($record->doctor->user->full_name) . '
                        </a>
                        <span class="text-xs text-gray-400">' . e($record->doctor->user->email ?? __('messages.common.n/a')) . '</span>
                    </div>
                ')
                ->searchable(['users.first_name', 'users.last_name']),




            Tables\Columns\TextColumn::make('billingDocument.agreement.name')
                ->label('Convenio')
                ->sortable()
                ->searchable(),

            TextColumn::make('services_count')
                ->label('Servicios')
                ->getStateUsing(fn ($record) =>
                    ($record->consultations_count ?? 0) + ($record->procedures_count ?? 0)
                )
                ->sortable(),

    

Tables\Columns\TextColumn::make('service_datetime')
    ->label('Fecha de Servicio')
    ->view('tables.columns.rips_date_time')
    ->sortable(),


            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Fecha de Creación'),

            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Fecha de Actualización'),
        ])
    ->filters([

        /*SelectFilter::make('estado_envio')
            ->label('Estado de Envío')
            ->options([
                'accepted' => 'Aceptado',
                'rejected' => 'Rechazado',
                'pending'  => 'Pendiente',
            ])
            ->query(function (Builder $query, $state) {
                $query->whereHas('billingDocument', fn ($q) => $q->where('submission_status', $state));
            })
            ->indicator(function ($state): ?string {
                return match ($state) {
                    'accepted' => 'Estado: Aceptado',
                    'rejected' => 'Estado: Rechazado',
                    'pending'  => 'Estado: Pendiente',
                    default    => null,
                };
            }),*/
SelectFilter::make('convenio')
    ->label('Convenio')
    ->options(fn () => \App\Models\Rips\RipsTenantPayerAgreement::pluck('name', 'id')->toArray())
    ->query(function (Builder $query, $state) {
        $value = is_array($state) ? $state['value'] ?? null : $state;

        if (!empty($value)) {
            $query->whereHas('billingDocument', fn ($q) => $q->where('agreement_id', $value));
        }
    })


,

        DateRangeFilter::make('service_datetime')
            ->label('Fecha de Servicio')
            ->indicator(function ($state): ?string {
                if (!empty($state['start']) && !empty($state['end'])) {
                    return 'Fecha: ' . $state['start']->format('d/m/Y') . ' - ' . $state['end']->format('d/m/Y');
                }
                return null;
            }),

    



        Filter::make('document_number')
            ->form([
                TextInput::make('document_number')->label('Número de Factura'),
            ])
            ->query(function (Builder $query, array $data) {
                if (!empty($data['document_number'])) {
                    $query->whereHas('billingDocument', function ($q) use ($data) {
                        $q->where('document_number', 'like', '%' . $data['document_number'] . '%');
                    });
                }
            })

        ->indicator(function (array $data): ?string {
            return !empty($data['document_number']) ? 'Factura: ' . $data['document_number'] : null;
        }),

        SelectFilter::make('patient_id')
            ->label('Paciente')
            ->options(Patient::getActivePatientNames()->toArray())
            ->query(function (Builder $query, $state) {
                $value = is_array($state) ? $state['value'] ?? null : $state;
                if (!empty($value)) {
                    $query->where('patient_id', $value);
                }
            })
            ->indicator(function ($state): ?string {
                $value = is_array($state) ? $state['value'] ?? null : $state;
                $patient = $value ? Patient::with('user')->find($value) : null;
                return $patient && $patient->user
                    ? 'Paciente: ' . $patient->user->full_name
                    : null;
            })

    ])




->actions([
    Tables\Actions\ViewAction::make()
        ->icon('heroicon-o-eye')
        ->iconButton()
        ->color('info'),
        
    Tables\Actions\EditAction::make()
        ->icon('heroicon-o-pencil-square')
        ->iconButton()
        ->color('primary'),

])

        ->bulkActions([
            // Agrupación de acciones por lotes
            Tables\Actions\BulkActionGroup::make([
                // Acción para eliminar registros seleccionados
                Tables\Actions\DeleteBulkAction::make(),

                // Acción para generar RIPS desde los servicios seleccionados
                Tables\Actions\BulkAction::make('generateRips')
                    ->label('Generar RIPS')
                    ->action(function ($records) {
                        $service = app(RipsGeneratorService::class);
                        return $service->generateByPatientServices($records);
                    })
                    ->icon('heroicon-o-document-text')
                    ->color('primary'),

                // Acción para generar y enviar RIPS a la API externa
                Tables\Actions\BulkAction::make('generarYEnviarRips')
                    ->label('Generar y Enviar RIPS')
                    ->action(function ($records) {
                        $service = app(\App\Services\RipsCoordinatorService::class);

                        // ID del tenant actual autenticado
                        $tenantId = auth()->user()->tenant_id;

                        // Agrupar los registros por convenio (agreement_id)
                        $grouped = $records->groupBy(fn($r) => optional($r->billingDocument)->agreement_id);

                        foreach ($grouped as $agreementId => $items) {
                            if (!$agreementId) continue;

                            // Rango de fechas del servicio por grupo
                            $start = $items->pluck('service_datetime')->filter()->min();
                            $end = $items->pluck('service_datetime')->filter()->max();

                            if (!$start || !$end) continue;

                            // Procesar y enviar cada grupo de facturas por convenio
                            $service->procesarYEnviarRips(
                                tenantId: $tenantId,
                                agreementId: $agreementId,
                                startDate: \Carbon\Carbon::parse($start)->format('Y-m-d'),
                                endDate: \Carbon\Carbon::parse($end)->format('Y-m-d')
                            );
                        }
                    })
                    ->requiresConfirmation() // Confirmación antes de ejecutar
                    ->color('success')
                    ->icon('heroicon-o-paper-airplane'),
                ]),
        ]);

    }
}




