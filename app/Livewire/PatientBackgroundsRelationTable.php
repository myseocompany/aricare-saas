<?php

namespace App\Livewire;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientBackgroundResource\PatientBackgroundResource;
use App\Models\Rda\PatientBackground;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class PatientBackgroundsRelationTable extends Component implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    public int $patientId;

    public function mount(): void
    {
        $patientId = (int) Route::current()?->parameter('record');

        abort_if($patientId === 0, 404);

        $this->patientId = $patientId;
    }

    protected function getQuery(): Builder
    {
        return PatientBackground::query()
            ->with(['backgroundType', 'cie10', 'cups'])
            ->where('patient_id', $this->patientId)
            ->orderByDesc('start_date');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('backgroundType.name')
                    ->label(__('messages.background_type'))
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('messages.common.description'))
                    ->wrap()
                    ->limit(80),
                TextColumn::make('cie10.code')
                    ->label('CIE10')
                    ->formatStateUsing(fn ($state, $record) => $state ? $state.' - '.$record->cie10?->description : __('messages.common.n/a'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')
                    ->label(__('messages.encounter_start_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(__('messages.encounter_end_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label('Activo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? __('messages.common.active') : __('messages.common.deactive'))
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
            ])
            ->headerActions([
                Action::make('createBackground')
                    ->label(__('messages.backgrounds_create'))
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->url(fn () => PatientBackgroundResource::getUrl('create', [
                        'patient_id' => $this->patientId,
                    ])),
            ])
            ->actions([
                Action::make('edit')
                    ->label(__('messages.common.edit'))
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->url(fn ($record) => PatientBackgroundResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()
                    ->label(__('messages.common.delete')),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public function render()
    {
        return view('livewire.patient-backgrounds-relation-table');
    }
}
