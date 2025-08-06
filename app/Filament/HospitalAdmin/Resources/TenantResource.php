<?php

namespace App\Filament\HospitalAdmin\Resources;

use App\Filament\HospitalAdmin\Resources\TenantResource\Pages;
use App\Filament\HospitalAdmin\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'fas-gears';
    protected static ?int $navigationSort = 99; // un número alto para que aparezca de último

    public static function getNavigationLabel(): string
    {
        return 'Ajustes';
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('rips_idsispro')
                    ->label(__('messages.rips.tenant.rips_idsispro'))
                    ->maxLength(50),

                Forms\Components\TextInput::make('rips_passispro')
                    ->label(__('messages.rips.tenant.rips_passispro'))
                    ->password()
                    ->maxLength(50),

                Forms\Components\Select::make('rips_identification_type_id')
                    ->label(__('messages.rips.tenant.rips_identification_type_id'))
                    ->options(
                        \App\Models\Rips\RipsIdentificationType::pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('rips_identification_number')
                    ->label(__('messages.rips.tenant.rips_identification_number'))
                    ->maxLength(20),
                /*
                Forms\Components\TextInput::make('tenant_username')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('hospital_name')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('document_number')
                    ->maxLength(20),
                Forms\Components\TextInput::make('document_type')
                    ->maxLength(5),
                Forms\Components\Toggle::make('is_billing_enabled'),
                Forms\Components\TextInput::make('provider_code')
                    ->maxLength(12),
                Forms\Components\TextInput::make('tax_identifier')
                    ->maxLength(12),
                    
                    */
   
                    /*
                Forms\Components\TextInput::make('sispro_username')
                    ->maxLength(100),
                Forms\Components\TextInput::make('sispro_password')
                    ->password()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('location_code')
                    ->maxLength(12),
                Forms\Components\Textarea::make('data')
                    ->columnSpanFull(),
                    */

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            ])
            ->filters([
                //
            ])
            ->actions([
                
            ])
            ->bulkActions([
               
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
           // 'index' => Pages\ListTenants::route('/'),
           // 'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
            'view' => Pages\ViewTenant::route('/{record}'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        // Obtenemos el tenant_id del usuario autenticado
        $tenantId = auth()->user()->tenant_id;

        // Redirigir directo a la página de edición (o show si la creas)
        return static::getUrl('view', ['record' => $tenantId]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Solo mostrar si el usuario tiene un tenant asignado
        return auth()->check() && auth()->user()->tenant_id !== null;
    }
    protected function getRedirectUrl(): string
    {
        return url()->previous(); // o alguna ruta segura como el dashboard
    }


}
