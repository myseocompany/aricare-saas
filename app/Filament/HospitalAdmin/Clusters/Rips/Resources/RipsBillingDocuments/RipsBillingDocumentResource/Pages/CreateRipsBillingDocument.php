<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRipsBillingDocument extends CreateRecord
{
    protected static string $resource = RipsBillingDocumentResource::class;
}
