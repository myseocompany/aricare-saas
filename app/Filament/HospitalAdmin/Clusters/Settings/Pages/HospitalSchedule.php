<?php

namespace App\Filament\HospitalAdmin\Clusters\Settings\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Arr;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
use Filament\Pages\SubNavigationPosition;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\HospitalAdmin\Clusters\Settings;
use App\Models\HospitalSchedule as HospitalScheduleModel;
use Illuminate\Support\Facades\Log;

class HospitalSchedule extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.hospital-admin.clusters.settings.pages.hospital-schedule';

    protected static ?string $cluster = Settings::class;

    public ?array $data = [];

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('messages.hospital_schedules');
    }

    public function getTitle(): string
    {
        return __('messages.hospital_schedules');
    }

    public static function  canAccess(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    public function mount()
{
    $tenantId = getLoggedInUser()->tenant_id;
    $schedules = HospitalScheduleModel::where('tenant_id', $tenantId)->get();

    $defaultStartTimes = array_fill(1, 7, "00:00");
    $defaultEndTimes = array_fill(1, 7, "23:45");
    $defaultCheckboxes = array_fill(1, 7, false); // ← esto es clave

    // Rellenar con valores de la BD si existen
    $startTimes = array_replace($defaultStartTimes, $schedules->pluck('start_time', 'day_of_week')->toArray());
    $endTimes = array_replace($defaultEndTimes, $schedules->pluck('end_time', 'day_of_week')->toArray());
    $checkboxes = array_replace(
        $defaultCheckboxes,
        $schedules->pluck('is_active', 'day_of_week')->map(fn($v) => (bool)$v)->toArray()
    );

    // Preparamos el resultado para llenar el formulario
    $this->form->fill([
        'start_time' => $startTimes,
        'end_time' => $endTimes,
        'day_of_week' => $checkboxes,
    ]);
}



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema(static::getFields())->columns(4)
            ])->statePath('data');
    }

    public static function getFields()
    {
        $fields = [];
    
        // Días completos en español
        $dayLabels = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    
        foreach ($dayLabels as $index => $label) {
            $fields[] = \Filament\Forms\Components\Grid::make(7)
                ->schema([
                    // Día como texto
                    \Filament\Forms\Components\Placeholder::make("label_$index")
                        ->content($label)
                        ->disableLabel()
                        ->columnSpan(1),
    
                    // Desde
                    Select::make("start_time.$index")
                        ->label('')
                        ->native(false)
                        ->searchable()
                        ->options(getSchedulesTimingSlot())
                        ->columnSpan(2),
    
                    // Hasta
                    Select::make("end_time.$index")
                        ->label('')
                        ->native(false)
                        ->searchable()
                        ->options(getSchedulesTimingSlot())
                        ->columnSpan(2),
    
                    // Botón copiar
                    $index > 1
                        ? \Filament\Forms\Components\Actions::make([
                            \Filament\Forms\Components\Actions\Action::make("copy_previous_$index")
                                ->action(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set) use ($index) {
                                    $set("start_time.$index", $get("start_time." . ($index - 1)));
                                    $set("end_time.$index", $get("end_time." . ($index - 1)));
                                })
                                ->iconButton()
                                ->icon('heroicon-o-arrow-uturn-left')
                                ->tooltip('Copiar horario anterior'),
                        ])->columnSpan(1)
                        : \Filament\Forms\Components\Placeholder::make("empty_$index")
                            ->content('')
                            ->disableLabel()
                            ->columnSpan(1),
    
                    // Checkbox final (activo o no)
                    Checkbox::make("day_of_week.$index")
                        ->label('')
                        ->extraAttributes(['class' => 'h-7 w-7'])
                        ->columnSpan(1),
                ])
                ->columnSpanFull();
        }
    
        return $fields;
    }
    
    

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->requiresConfirmation()
                ->label(__('messages.common.save'))
                ->submit('save')
        ];
    }

    // protected function getViewData(): array
    // {
    //     $hospitalSchedules = HospitalScheduleModel::all();
    //     $weekDays = HospitalScheduleModel::WEEKDAY_FULL_NAME;
    //     $weekDay = HospitalScheduleModel::WEEKDAY;
    //     $slots = getSchedulesTimingSlot();

    //     return [
    //         'hospitalSchedules' => $hospitalSchedules,
    //         'weekDays' => $weekDays,
    //         'weekDay' => $weekDay,
    //         'slots' => $slots
    //     ];
    // }
    public function save()
    {
        $tenantId = getLoggedInUser()->tenant_id;
    
        $this->data['start_time'] = $this->data['start_time'] ?? [];
        $this->data['end_time'] = $this->data['end_time'] ?? [];
        $this->data['day_of_week'] = $this->data['day_of_week'] ?? [];
    
        foreach (range(1, 7) as $day) {
            $isActive = $this->data['day_of_week'][$day] ?? false;
            $startTime = $this->data['start_time'][$day] ?? null;
            $endTime = $this->data['end_time'][$day] ?? null;
    
            if ($isActive && $startTime && $endTime && strtotime($startTime) > strtotime($endTime)) {
                return $this->sendError(HospitalScheduleModel::WEEKDAY[$day] . __('messages.new_change.time_invalid'));
            }
    
            $schedule = HospitalScheduleModel::where('tenant_id', $tenantId)
                ->where('day_of_week', $day)
                ->first();
    
            if ($schedule) {
                $schedule->update([
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_active' => $isActive,
                ]);
            } else {
                HospitalScheduleModel::create([
                    'tenant_id' => $tenantId,
                    'day_of_week' => $day,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_active' => $isActive,
                ]);
            }
        }
    
        Notification::make()
            ->success()
            ->title(__('messages.flash.hospital_schedule_saved'))
            ->send();
    
        $this->afterSave();
    }
    
    

    protected function afterSave()
    {
        $this->js('window.location.reload()');
    }
}
