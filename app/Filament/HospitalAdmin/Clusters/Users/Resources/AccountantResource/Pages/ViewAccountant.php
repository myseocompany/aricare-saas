<?php

namespace App\Filament\HospitalAdmin\Clusters\Users\Resources\AccountantResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\HospitalAdmin\Clusters\Users\Resources\AccountantResource;
use App\Filament\HospitalAdmin\Clusters\Users\Resources\AccountantResource\RelationManagers\PayrollsRelationManager;

class ViewAccountant extends ViewRecord
{
    protected static string $resource = AccountantResource::class;

    protected static string $view = 'filament.resources.users.pages.view-accountant';

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
