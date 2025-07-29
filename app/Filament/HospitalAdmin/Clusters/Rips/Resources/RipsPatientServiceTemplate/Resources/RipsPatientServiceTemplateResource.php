<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPatientServiceTemplate\Resources;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\TemplatesCluster;




use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPatientServiceTemplate\Resources\Pages;

use App\Models\Rips\RipsPatientServiceTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

use App\Filament\HospitalAdmin\Clusters\RipsCluster;

class RipsPatientServiceTemplateResource extends Resource
{
    protected static ?string $model = RipsPatientServiceTemplate::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;


    protected static ?string $cluster = RipsCluster::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción'),

                Forms\Components\Toggle::make('is_public')
                    ->label('¿Visible para todos los usuarios del tenant?'),
            ]);
    }


        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    Tables\Columns\TextColumn::make('name')->label('Nombre'),
                    Tables\Columns\TextColumn::make('description')->label('Descripción')->limit(50),
                    Tables\Columns\IconColumn::make('is_public')
                        ->boolean()
                        ->label('¿Público?'),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Creado')
                        ->dateTime('d/m/Y H:i'),
                ])
                ->filters([
                    //
                ])
                ->actions([
                    Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListRipsPatientServiceTemplates::route('/'),
            'create' => Pages\CreateRipsPatientServiceTemplate::route('/create'),
            'edit' => Pages\EditRipsPatientServiceTemplate::route('/{record}/edit'),
        ];
    }
    public static function getModelLabel(): string
    {
        return __('messages.rips.patientservicetemplate.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.rips.patientservicetemplate.title_plural');
    }

}
