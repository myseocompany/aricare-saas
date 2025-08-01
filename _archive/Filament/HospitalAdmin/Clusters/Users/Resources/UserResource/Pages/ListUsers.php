<?php

namespace App\Filament\HospitalAdmin\Clusters\Users\Resources\UserResource\Pages;

use Filament\Actions;
use Tables\Columns\TextColumn;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;
use App\Filament\HospitalAdmin\Clusters\Users\Resources\UserResource;
use Filament\Infolists\Components\TextEntry;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
