<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsTenantPayerAgreement;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocumentsCluster;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsTenantPayerAgreement\RipsTenantPayerAgreementResource\Pages;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsTenantPayerAgreement\RipsTenantPayerAgreementResource\RelationManagers;
use App\Models\Rips\RipsTenantPayerAgreement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HospitalAdmin\Clusters\RipsCluster;

use Filament\Pages\SubNavigationPosition;

class RipsTenantPayerAgreementResource extends Resource
{
    protected static ?string $model = RipsTenantPayerAgreement::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $cluster = RipsCluster::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('tenant_id')
                    ->default(fn () => auth()->user()->tenant_id)
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label(__('messages.rips.payer_agreement.name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('code')
                    ->label(__('messages.rips.payer_agreement.code'))
                    ->required()
                    ->maxLength(50),

            ]);
    }


    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label(__('messages.rips.payer_agreement.name'))
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('code')
                ->label(__('messages.rips.payer_agreement.code'))
                ->sortable()
                ->searchable(),

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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRipsTenantPayerAgreements::route('/'),
            'create' => Pages\CreateRipsTenantPayerAgreement::route('/create'),
            'edit' => Pages\EditRipsTenantPayerAgreement::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('messages.rips.payer_agreement.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.rips.payer_agreement.title_plural');
    }

}
