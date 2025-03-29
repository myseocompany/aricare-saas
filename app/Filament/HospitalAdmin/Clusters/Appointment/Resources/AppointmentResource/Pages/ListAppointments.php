<?php

namespace App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\Doctor;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Contracts\Database\Query\Builder;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentResource;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;
    protected  $i = 1;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('googleCalendar')
                ->hiddenLabel()
                ->url(route('filament.hospitalAdmin.appointment.pages.appointment-calendar'))
                ->icon('heroicon-s-calendar'),
            Actions\CreateAction::make(),
            ExportAction::make()->icon('heroicon-o-arrow-right-start-on-rectangle')
                ->disabled(!AppointmentResource::getModel()::whereTenantId(getLoggedInUser()->tenant_id)->exists())
                ->hidden(function () {
                    if (auth()->user()->hasRole(['Doctor'])) {
                        return false;
                    }
                    return true;
                })
                ->label(__('messages.common.export_to_excel'))->exports([
                    ExcelExport::make()
                        ->withFilename(__('messages.appointments') . '-' . now()->format('Y-m-d') . '.xlsx')
                        ->modifyQueryUsing(function (Builder $query) {
                            $query->where('tenant_id', auth()->user()->tenant_id);
                            if (! getLoggedinDoctor()) {
                                if (getLoggedinPatient()) {
                                    $patient = Auth::user();
                                    $query->whereHas('patient', function (Builder $query) use ($patient) {
                                        $query->where('user_id', '=', $patient->id);
                                    });
                                }
                            } else {
                                $doctorId = Doctor::where('user_id', getLoggedInUserId())->first();
                                $query = $query->where('doctor_id', $doctorId->id);
                            }
                            return $query;
                        })
                        ->withColumns([
                            Column::make('id')->heading('No')->formatStateUsing(function () {
                                return $this->i++;
                            }),
                            Column::make('patient.user.full_name')->heading(heading: __('messages.case.patient')),
                            Column::make('doctor.user.full_name')->heading(heading: __('messages.role.doctor')),
                            Column::make('doctor.department.title')->heading(heading: __('messages.doctor_department.doctor_department')),
                            Column::make('doctor.description')->heading(heading: __('messages.common.description')),
                            Column::make('opd_date')->heading(heading: __('messages.case.date')),

                        ]),

                ]),

        ];
    }
}
