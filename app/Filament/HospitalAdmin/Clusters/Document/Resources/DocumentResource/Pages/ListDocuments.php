<?php

namespace App\Filament\HospitalAdmin\Clusters\Document\Resources\DocumentResource\Pages;

use Filament\Actions;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ListRecords;
use App\Filament\HospitalAdmin\Clusters\Document\Resources\DocumentResource;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('messages.document.new_document'))->modalWidth('3xl')->createAnother(false)->modalHeading(__('messages.document.new_document'))->successNotificationTitle(__('messages.flash.document_saved'))
                ->using(function ($data): Model {
                    if (getLoggedInUser()->hasRole('Patient')) {
                        $data['patient_id'] = Patient::where('user_id', $data['uploaded_by'])->first()->id;
                    }
                    return DocumentResource::getModel()::create($data);
                }),
        ];
    }
}
