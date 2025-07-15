<?php

namespace App\Filament\HospitalAdmin\Clusters\Inventory\Resources\ItemStockResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Repositories\ItemStockRepository;
use App\Filament\HospitalAdmin\Clusters\Inventory\Resources\ItemStockResource;

class EditItemStock extends EditRecord
{
    protected static string $resource = ItemStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }
    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.flash.item_stock_updated');
    }

    protected function handleRecordUpdate(Model $record, array $input): Model
    {
        $itemStock = $record;
        $input['purchase_price'] = removeCommaFromNumbers($input['purchase_price']);
        app(ItemStockRepository::class)->update($itemStock, $input);

        $record = new ($this->getModel())($input);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
