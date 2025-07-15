<?php

namespace App\Filament\HospitalAdmin\Clusters\Inventory\Resources\IssuedItemResource\Pages;

use Exception;
use Filament\Actions;
use App\Models\IssuedItem;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Repositories\IssuedItemRepository;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\HospitalAdmin\Clusters\Inventory\Resources\IssuedItemResource;

class CreateIssuedItem extends CreateRecord
{
    protected static string $resource = IssuedItemResource::class;
    protected static bool $canCreateAnother = false;

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $input): Model
    {
        $input['return_date'] = ! empty($input['return_date']) ? $input['return_date'] : null;
        try {
            DB::beginTransaction();

            $issuedItem = IssuedItem::create($input);
            $newItemAvailableQty = $issuedItem->item->available_quantity - $issuedItem->quantity;
            $issuedItem->item()->update(['available_quantity' => $newItemAvailableQty]);

            DB::commit();

            return $issuedItem;
        } catch (Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
            $this->halt();
        }
        return $issuedItem;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.flash.issued_item_saved');
    }
}
