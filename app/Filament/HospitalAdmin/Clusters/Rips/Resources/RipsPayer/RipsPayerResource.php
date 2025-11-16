<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayer;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayer\RipsPayerResource\Form\PayerForm;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayer\RipsPayerResource\Pages;
use App\Filament\HospitalAdmin\Clusters\RipsCluster;
use App\Models\Rips\RipsPayer;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RipsPayerResource extends Resource
{
    protected static ?string $model = RipsPayer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $cluster = RipsCluster::class;

    public static function form(Form $form): Form
    {
        return $form->schema(PayerForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type.name')
                    ->label(__('messages.rips.payer.type_id'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('messages.rips.payer.name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('identification')
                    ->label(__('messages.rips.payer.identification'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('messages.rips.payer.phone'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('messages.rips.payer.email'))
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRipsPayers::route('/'),
            'create' => Pages\CreateRipsPayer::route('/create'),
            'edit' => Pages\EditRipsPayer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Auth::user()->tenant_id)
            ->with('type');
    }

    public static function getModelLabel(): string
    {
        return __('messages.rips.payer.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.rips.payer.title_plural');
    }
}
