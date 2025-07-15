<?php

namespace App\Filament\HospitalAdmin\Clusters\HospitalCharge\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Charge;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ChargeCategory;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use App\Repositories\ChargeRepository;
use function Laravel\Prompts\textarea;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Google\Service\ShoppingContent\Amount;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HospitalAdmin\Clusters\HospitalCharge;
use App\Filament\HospitalAdmin\Clusters\HospitalCharge\Resources\ChargeResource\Pages;
use App\Filament\HospitalAdmin\Clusters\HospitalCharge\Resources\ChargeResource\RelationManagers;

class ChargeResource extends Resource
{
    protected static ?string $model = Charge::class;

    protected static ?string $cluster = HospitalCharge::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole(['Admin'])  && !getModuleAccess('Charges')) {
            return false;
        } elseif (!auth()->user()->hasRole(['Admin']) && !getModuleAccess('Charges')) {
            return false;
        }
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.charges');
    }

    public static function getLabel(): string
    {
        return __('messages.charges');
    }

    public static function canCreate(): bool
    {
        if(auth()->user()->hasRole(['Admin','Receptionist']) && getModuleAccess('Charges'))
        {
            return true;
        }
        return false;
    }
    public static function canEdit(Model $record): bool
    {
        if(auth()->user()->hasRole(['Admin','Receptionist']) && getModuleAccess('Charges'))
        {
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if(auth()->user()->hasRole(['Admin','Receptionist']) && getModuleAccess('Charges'))
        {
            return true;
        }
        return false;
    }

    public static function canViewAny(): bool
    {
        if(auth()->user()->hasRole(['Admin','Receptionist']))
        {
            return true;
        }
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('charge_type')
                    ->label(__('messages.charge_category.charge_type') . ':')
                    ->options(ChargeCategory::CHARGE_TYPES)
                    ->placeholder(__('messages.charge_category.select_charge_type'))
                    ->required()
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->validationMessages([
                        'required' => __('messages.fields.the') . ' ' . __('messages.charge_category.charge_type') . ' ' . __('messages.fields.required'),
                    ]),
                Select::make('charge_category_id')
                    ->live()
                    ->options(function ($get) {
                        if ($get('charge_type') == 1) {
                            return ChargeCategory::where('charge_type', 1)->whereTenantId(auth()->user()->tenant_id)->pluck('name', 'id');
                        } elseif ($get('charge_type') == 2) {
                            return ChargeCategory::where('charge_type', 2)->whereTenantId(auth()->user()->tenant_id)->pluck('name', 'id');
                        } elseif ($get('charge_type') == 3) {
                            return ChargeCategory::where('charge_type', 3)->whereTenantId(auth()->user()->tenant_id)->pluck('name', 'id');
                        } elseif ($get('charge_type') == 4) {
                            return ChargeCategory::where('charge_type', 4)->whereTenantId(auth()->user()->tenant_id)->pluck('name', 'id');
                        } elseif ($get('charge_type') == 5) {
                            return ChargeCategory::where('charge_type', 5)->whereTenantId(auth()->user()->tenant_id)->pluck('name', 'id');
                        } else {
                            [
                                '' => __('messages.new_change.no_records_found'),
                            ];
                        }
                    })
                    ->label(__('messages.charge.charge_category') . ':')
                    ->placeholder(__('messages.pathology_category.select_charge_category'))
                    ->native(false)
                    ->searchable()
                    ->required()
                    ->validationMessages([
                        'required' => __('messages.fields.the') . ' ' . __('messages.charge.charge_category') . ' ' . __('messages.fields.required'),
                    ]),
                TextInput::make('code')
                    ->label(__('messages.charge.code') . ':')
                    ->placeholder(__('messages.charge.code'))
                    ->required()
                    ->validationMessages([
                        'unique' => __('messages.charge.code') . ' ' . __('messages.common.is_already_exists'),
                    ])
                    ->maxLength(255),
                TextInput::make('standard_charge')
                    ->label(__('messages.charge.standard_charge') . ':')
                    ->placeholder(__('messages.charge.standard_charge'))
                    ->required()
                    ->validationAttribute(__('messages.charge.standard_charge'))
                    ->maxLength(255)
                    ->numeric()
                    ->minValue(1),
                Textarea::make('description')
                    ->label(__('messages.common.description') . ':')
                    ->placeholder(__('messages.common.description'))
                    ->rows(4)
                    ->required()
                    ->validationAttribute(__('messages.common.description'))
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist']) && !getModuleAccess('Charges')) {
            abort(404);
        }

        return
            $table = $table->modifyQueryUsing(function (Builder $query) {
                $query->whereTenantId(auth()->user()->tenant_id);
                return $query;
            })
            ->paginated([10,25,50])
            ->defaultSort('id','desc')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('messages.charge.code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('chargeCategory.name')
                    ->label(__('messages.charge.charge_category'))
                    ->searchable()
                    ->color('primary')
                    ->formatStateUsing(fn($record) => "<a href='" . ChargeResource::getUrl('view', ['record' => $record->id]) . "' onmouseover=''>" . $record->chargeCategory->name . "</a>")
                    ->html()
                    ->sortable(),
                Tables\Columns\TextColumn::make('charge_type')
                    ->label(__('messages.charge_category.charge_type'))
                    ->getStateUsing(function ($record) {
                        if ($record->charge_type == 1) {
                            return __('messages.charge_filter.investigation');
                        } elseif ($record->charge_type == 2) {
                            return __('messages.charge_filter.operation_theater');
                        } elseif ($record->charge_type == 3) {
                            return __('messages.charge_filter.others');
                        } elseif ($record->charge_type == 4) {
                            return __('messages.charge_filter.procedure');
                        } else {
                            return __('messages.charge_filter.supplier');
                        }
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->charge_type == 1) {
                            return 'primary';
                        } elseif ($record->charge_type == 2) {
                            return 'info';
                        } elseif ($record->charge_type == 3) {
                            return 'success';
                        } elseif ($record->charge_type == 4) {
                            return 'danger';
                        } else {
                            return 'warning';
                        }
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('standard_charge')
                    ->label(__('messages.charge.standard_charge'))
                    ->getStateUsing(fn($record) => getCurrencyFormat($record->standard_charge) ?? __('messages.common.n/a'))
                    ->searchable()
                    ->sortable(),
            ])
            ->recordAction(null)
            ->recordUrl(null)
            ->filters([
                SelectFilter::make('charge_type')
                    ->label(__('messages.common.status'))
                    ->options([
                        '' => __('messages.filter.all'),
                        1 => __('messages.charge_filter.investigation'),
                        4 => __('messages.charge_filter.procedure'),
                        5 => __('messages.charge_filter.supplier'),
                        2 => __('messages.charge_filter.operation_theater'),
                        3 => __('messages.charge_filter.others'),
                    ])->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton()->successNotificationTitle(__('messages.flash.charge_updated'))->before(fn($record, $data, $action) =>  getUniqueCodeValidation(static::getModel(), $record, $data, $action, isEdit: true)),
                Tables\Actions\DeleteAction::make()->iconButton()->successNotificationTitle(__('messages.flash.charge_deleted')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\ViewCharges::route('/{record}'),
            'index' => Pages\ManageCharges::route('/'),
        ];
    }
}
