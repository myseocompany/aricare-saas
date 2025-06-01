<?php

namespace App\Filament\Resources;

// Importamos clases necesarias para construir el recurso
use App\Filament\Resources\RipsReportingCenterResource\Pages;
use App\Services\RipsGeneratorService;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RipsReportingCenterResource extends Resource
{
    protected static ?string $model = null;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Generador RIPS';
    protected static ?string $navigationGroup = 'Reportes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Select::make('agreement_id')
                        ->label('Convenio')
                        ->options(\App\Models\Agreement::pluck('name', 'id'))
                        ->required(),
                    
                    DatePicker::make('start_date')
                        ->label('Fecha inicial')
                        ->required(),
                        
                    DatePicker::make('end_date')
                        ->label('Fecha final')
                        ->required()
                        ->minDate(fn ($get) => $get('start_date'))
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\GenerateRipsReport::route('/'),
        ];
    }
}
