<?php

namespace App\Filament\HospitalAdmin\Clusters\HospitalCharge\Resources\ChargeResource\Pages;

use Filament\Actions;
use App\Models\ChargeCategory;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\HospitalAdmin\Clusters\HospitalCharge\Resources\ChargeResource;

class ViewCharges extends ViewRecord
{
    protected static string $resource = ChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('back')
                ->label(__('messages.common.back'))
                ->outlined()
                ->url(url()->previous()),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()->schema([
                    TextEntry::make('charge_type')
                        ->formatStateUsing(function ($record) {
                            $chargeTypes = ChargeCategory::CHARGE_TYPES;
                            asort($chargeTypes);
                            return $chargeTypes[$record->charge_type];
                        })
                        ->label(__('messages.charge_category.charge_type')),
                    TextEntry::make('chargeCategory.name')
                        ->default(__('messages.common.n/a'))
                        ->label(__('messages.charge.charge_category')),
                    TextEntry::make('code')
                        ->default(__('messages.common.n/a'))
                        ->label(__('messages.charge.code')),
                    TextEntry::make('standard_charge')
                        ->default(__('messages.common.n/a'))
                        ->formatStateUsing(fn($state): string => getCurrencyFormat($state))
                        ->label(__('messages.charge.standard_charge')),
                    TextEntry::make('created_at')
                        ->label(__('messages.common.created_at'))
                        ->since(),
                    TextEntry::make('updated_at')
                        ->label(__('messages.common.last_updated'))
                        ->since(),
                    TextEntry::make('description')
                        ->default(__('messages.common.n/a'))
                        ->label(__('messages.death_report.description')),
                ])->columns(2)
            ]);
    }
}
