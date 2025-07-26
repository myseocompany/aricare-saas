<?php

namespace App\Filament\HospitalAdmin\Clusters\Doctors\Resources;


use App\Models\User;
use Filament\Tables;
use App\Models\Doctor;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Table;
use App\Models\HospitalSchedule;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\ScheduleRepository;
use Filament\Pages\SubNavigationPosition;
use Filament\Forms\Components\Select;

use App\Filament\HospitalAdmin\Clusters\Doctors;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\HospitalAdmin\Clusters\Doctors\Resources\SchedulesResource\Pages;

class SchedulesResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Doctors::class;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole('Admin') && !getModuleAccess('Schedules')) {
            return false;
        } elseif (!auth()->user()->hasRole('Admin') && !getModuleAccess('Schedules')) {
            return false;
        }
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.schedules');
    }

    public static function getLabel(): string
    {
        return __('messages.schedules');
    }

    public static function canCreate(): bool
    {
        if (auth()->user()->hasRole('Admin') && getModuleAccess('Schedules')) {
            return true;
        }
        return false;
    }
    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->hasRole('Admin') && getModuleAccess('Schedules')) {
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (auth()->user()->hasRole('Admin') && getModuleAccess('Schedules')) {
            return true;
        }
        return false;
    }

    public static function canViewAny(): bool
    {
        if (auth()->user()->hasRole('Admin')) {
            return true;
        }
        return false;
    }

    public static function form(Form $form): Form
    {
        $days = [
            ['id' => 1, 'name' => 'Lunes', 'value' => 'Monday'],
            ['id' => 2, 'name' => 'Martes', 'value' => 'Tuesday'],
            ['id' => 3, 'name' => 'Miércoles', 'value' => 'Wednesday'],
            ['id' => 4, 'name' => 'Jueves', 'value' => 'Thursday'],
            ['id' => 5, 'name' => 'Viernes', 'value' => 'Friday'],
            ['id' => 6, 'name' => 'Sábado', 'value' => 'Saturday'],
            ['id' => 7, 'name' => 'Domingo', 'value' => 'Sunday'],
        ];
    
        $daysOfWeek = HospitalSchedule::pluck('day_of_week')->toArray();
    
        return $form
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('doctor_id')
                        ->label('Médico:')
                        ->required()
                        ->options(fn ($operation) => app(ScheduleRepository::class)->getData($operation)['doctors'])
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccionar nombre del médico')
                        ->validationMessages([
                            'required' => 'El campo médico es obligatorio.',
                        ])
                        ->columnSpan(2),
    
                    Forms\Components\TimePicker::make('per_patient_time')
                        ->label('Tiempo por paciente')
                        ->required()
                        ->default('00:00:00')
                        ->native(false)
                        ->columnSpan(1),
                ])->columns(3),
    
                Forms\Components\Section::make('')
                    ->schema(
                        collect($days)->map(function ($day, $i) use ($daysOfWeek) {
                            return Forms\Components\Grid::make(7)->schema([
                                // Día en texto
                                Forms\Components\Placeholder::make("day_$i")
                                    ->content($day['name'])
                                    ->disableLabel()
                                    ->columnSpan(2),
    
                                Select::make("schedule.$i.available_from")
                                    ->label('')
                                    ->native(false)
                                    ->searchable()
                                    ->options(getSchedulesTimingSlot())
                                    ->disableLabel()
                                    ->columnSpan(2),
                                
                                Select::make("schedule.$i.available_to")
                                    ->label('')
                                    ->native(false)
                                    ->searchable()
                                    ->options(getSchedulesTimingSlot())
                                    ->disableLabel()
                                    ->columnSpan(2),
                                    
                                // Botón copiar
                                $i > 0
                                    ? Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make("copy_previous_$i")
                                            ->action(function (Forms\Get $get, Forms\Set $set) use ($i) {
                                                $set("schedule.$i.available_from", $get("schedule." . ($i - 1) . ".available_from"));
                                                $set("schedule.$i.available_to", $get("schedule." . ($i - 1) . ".available_to"));
                                            })
                                            ->iconButton()
                                            ->icon('heroicon-o-arrow-uturn-left')
                                            ->tooltip('Copiar horario anterior'),
                                    ])->columnSpan(1)
                                    : Forms\Components\Placeholder::make("placeholder_$i")
                                        ->content('')
                                        ->disableLabel()
                                        ->columnSpan(1),
    
                                Forms\Components\Hidden::make("schedule.$i.available_on")
                                    ->default($day['id']),
                            ])
                            ->visible(fn (Forms\Get $get) => in_array($day['id'], $daysOfWeek));
                        })->toArray()
                    )
                    ->columns(1),
            ]);
    }
    



    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole('Admin') && !getModuleAccess('Schedules')) {
            abort(404);
        }
        return $table = $table->modifyQueryUsing(function ($query) {
            return $query->where('tenant_id', getLoggedInUser()->tenant_id);
        })
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('doctor.user.profile')->collection(User::COLLECTION_PROFILE_PICTURES)->rounded()->label(__('messages.case.doctor'))->width(50)->height(50)
                    ->url(fn($record) => DoctorResource::getUrl('view', ['record' => $record->doctor->id]))
                    ->sortable(['first_name'])
                    ->defaultImageUrl(function ($record) {
                        if (!$record->doctor->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->doctor->user->first_name);
                        }
                    }),
                TextColumn::make('doctor.user.full_name')
                    ->label('')
                    ->description(function ($record) {
                        return $record->doctor->user->email;
                    })
                    ->formatStateUsing(fn($record) => '<a href="' . DoctorResource::getUrl('view', ['record' => $record->doctor->id]) . '" class="hoverLink">' . $record->doctor->user->full_name . '</a>')
                    ->html()
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->searchable(['first_name', 'last_name', 'email']),
                TextColumn::make('per_patient_time')
                    ->label(__('messages.schedule.per_patient_time'))
                    ->getStateUsing(function ($record) {
                        $time = \Carbon\Carbon::createFromFormat('H:i:s', $record->per_patient_time)->format('H:i');
                        if ($time > '00:59:00') {
                            return $time . '  ' . __('messages.hours');
                        } else {
                            return   \Carbon\Carbon::createFromFormat('H:i:s', $record->per_patient_time)->format('i') . '  ' . __('messages.minutes');
                        }
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordAction(null)
            ->actions([
                Tables\Actions\ViewAction::make()->color('info')->iconButton(),
                Tables\Actions\EditAction::make()->iconButton(),
            ])
            ->recordUrl(false)
            ->actionsColumnLabel(__('messages.common.action'))
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedules::route('/create'),
            'view' => Pages\ViewSchedules::route('/{record}'),
            'edit' => Pages\EditSchedules::route('/{record}/edit'),
        ];
    }

    function getSchedulesTimingSlot(): array {
        $slots = [];
        for ($hour = 0; $hour < 24; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 15) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $slots[$time] = $time;
            }
        }
        return $slots;
    }
    
}
