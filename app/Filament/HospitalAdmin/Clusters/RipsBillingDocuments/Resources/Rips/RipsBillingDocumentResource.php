<?php

namespace App\Filament\HospitalAdmin\Clusters\RipsBillingDocuments\Resources\Rips;

use App\Filament\HospitalAdmin\Clusters\RipsBillingDocuments;
use App\Filament\HospitalAdmin\Clusters\RipsBillingDocuments\Resources\Rips\RipsBillingDocumentResource\Pages;
use App\Filament\HospitalAdmin\Clusters\RipsBillingDocuments\Resources\Rips\RipsBillingDocumentResource\RelationManagers;
use App\Models\Rips\RipsBillingDocument;
use App\Models\Rips\RipsBillingDocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

class RipsBillingDocumentResource extends Resource
{
    protected static ?string $model = RipsBillingDocument::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $cluster = RipsBillingDocuments::class;

    public static function getNavigationLabel(): string
    {
        return __('messages.rips_billing_document_navigation');
    }

    public static function getModelLabel(): string
    {
        return __('messages.rips_billing_document_model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.rips_billing_document_plural_model');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('tenant_id')
                    ->default(fn() => Auth::user()->tenant_id)
                    ->required(),
                Select::make('agreement_id')
                    ->label('Convenio')
                    ->relationship('agreement', 'name')
                    ->searchable()
                    ->required(),
                Radio::make('type_id')
                    ->label(__('messages.rips.billingdocument.type'))
                    ->options(fn () => RipsBillingDocumentType::pluck('name', 'id')->toArray())
                    ->inline()
                    ->required(),
                Forms\Components\TextInput::make('document_number')
                    ->label(__('messages.rips.billingdocument.document_number'))
                    ->required()
                    ->maxLength(30),
                Forms\Components\DateTimePicker::make('issued_at')
                    ->label(__('messages.rips.billingdocument.issued_at'))
                    ->required(),
                Forms\Components\TextInput::make('cufe')
                    ->maxLength(100),
                Forms\Components\TextInput::make('uuid_dian')
                    ->maxLength(100),
                Forms\Components\TextInput::make('total_amount')
                    ->label(__('messages.rips.billingdocument.total_amount'))
                    ->numeric(),
                Forms\Components\TextInput::make('copay_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('discount_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('net_amount')
                    ->numeric(),
                FileUpload::make('xml_path')
                    ->label('Archivo XML')
                    ->disk('public')
                    ->directory(fn ($get, $record) =>
                        Auth::user()->tenant_id.'/'.($get('agreement_id') ?? $record?->agreement_id).'/'.($record?->patientServices()->first()?->patient_id ?? '0')
                    )
                    ->visibility('public')
                    ->preserveFilenames()
                    ->acceptedFileTypes(['text/xml','application/xml'])
                    ->downloadable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $table = $table->modifyQueryUsing(function (Builder $query) {
            return $query->with(['agreement', 'patientServices.patient', 'type']);
        });

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agreement.name')
                    ->label(__('messages.rips.billingdocument.agreement'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('patientServices')
                    ->label(__('messages.rips.billingdocument.patient'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->patientServices
                            ->map(fn($ps) => $ps->patient->full_name)
                            ->implode(', ');
                    })
                    ->limit(30),
                Tables\Columns\TextColumn::make('type.name')
                    ->label(__('messages.rips.billingdocument.type'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_number')
                    ->label(__('messages.rips.billingdocument.document_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('issued_at')
                    ->label(__('messages.rips.billingdocument.issued_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('messages.rips.billingdocument.total_amount'))
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListRipsBillingDocuments::route('/'),
            'create' => Pages\CreateRipsBillingDocument::route('/create'),
            'edit' => Pages\EditRipsBillingDocument::route('/{record}/edit'),
        ];
    }
}
