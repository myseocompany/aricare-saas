<?php

namespace App\Filament\HospitalAdmin\Clusters\BedManagement\Resources\BedTypeResource\Widgets;

use App\Models\Bed;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

class BedTypeList extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    var $record;

    public function mount($record)
    {
        $this->record = $record->id;
    }

    protected function getTableHeading(): string
    {
        return __('messages.beds');
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated([10,25,50])
            ->query(
                Bed::where('bed_type', $this->record)
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.bed_assign.bed'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('messages.common.description'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('charge')
                    ->label(__('messages.bed.charge'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('is_available')
                    ->label(__('messages.bed.available'))
                    ->formatStateUsing(function ($record) {
                        return $record->is_available == 1 ? __('messages.common.yes') : __('messages.common.no');
                    })
                    ->badge()
                    ->color(function ($record) {
                        return $record->is_available == 1 ? 'success' : 'danger';
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }
}
