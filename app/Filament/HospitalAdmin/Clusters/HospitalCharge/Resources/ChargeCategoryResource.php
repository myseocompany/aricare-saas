<?php

namespace App\Filament\HospitalAdmin\Clusters\HospitalCharge\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ChargeCategory;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HospitalAdmin\Clusters\HospitalCharge;
use App\Filament\HospitalAdmin\Clusters\HospitalCharge\Resources\ChargeCategoryResource\Pages;
use App\Filament\HospitalAdmin\Clusters\HospitalCharge\Resources\ChargeCategoryResource\RelationManagers;
use App\Models\RadiologyTest;
use Filament\Notifications\Notification;

class ChargeCategoryResource extends Resource
{
    protected static ?string $model = ChargeCategory::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole(['Admin'])  && !getModuleAccess('Charge Categories')) {
            return false;
        } elseif (!auth()->user()->hasRole(['Admin']) && !getModuleAccess('Charge Categories')) {
            return false;
        }
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.charge_categories');
    }

    public static function getLabel(): string
    {
        return __('messages.charge_categories');
    }

    public static function canCreate(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist']) && getModuleAccess('Charge Categories')) {
            return true;
        }
        return false;
    }
    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist']) && getModuleAccess('Charge Categories')) {
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist']) && getModuleAccess('Charge Categories')) {
            return true;
        }
        return false;
    }

    public static function canViewAny(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist'])) {
            return true;
        }
        return false;
    }

    protected static ?string $cluster = HospitalCharge::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('messages.charge.charge_category') . ':')
                    ->validationMessages([
                        'unique' => __('messages.user.name') . ' ' . __('messages.common.is_already_exists'),
                    ])
                    ->required(),
                Textarea::make('description')
                    ->label(__('messages.common.description') . ':')
                    ->rows(4)
                    ->placeholder(__('messages.common.description')),
                Select::make('charge_type')
                    ->label(__('messages.charge_category.charge_type') . ':')
                    ->options([
                        1 => __('messages.charge_filter.investigation'),
                        2 => __('messages.charge_filter.operation_theater'),
                        3 => __('messages.charge_filter.others'),
                        4 => __('messages.charge_filter.procedure'),
                        5 => __('messages.charge_filter.supplier'),
                    ])
                    ->native(false)
                    ->required()
                    ->preload()
                    ->searchable()
                    ->validationMessages([
                        'required' => __('messages.fields.the') . ' ' . __('messages.charge_category.charge_type') . ' ' . __('messages.fields.required'),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist']) && !getModuleAccess('Charge Categories')) {
            abort(404);
        }

        return
            $table = $table->modifyQueryUsing(function (Builder $query) {
                $query->whereTenantId(auth()->user()->tenant_id);
                return $query;
            })
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.charge.charge_category'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('messages.common.description'))
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->description ?? __('messages.common.n/a'))
                    ->sortable(),
                TextColumn::make('charge_type')
                    ->label(__('messages.charge_category.charge_type'))
                    ->searchable()
                    ->badge()
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
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton()->modalWidth("md")->successNotificationTitle(__('messages.flash.charge_category_updated'))->before(fn($record, $data, $action) =>  getUniqueNameValidation(static::getModel(), $record, $data, $action, isEdit: true)),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->action(function (ChargeCategory $record) {
                        if (! canAccessRecord(ChargeCategory::class, $record->id)) {
                            return Notification::make()
                                ->danger()
                                ->title(__('messages.flash.charge_category_not_found'))
                                ->send();
                        }
                        $chargeCategoryModels = [
                            RadiologyTest::class,
                        ];
                        $result = canDelete($chargeCategoryModels, 'charge_category_id', $record->id);
                        if ($result) {
                            return Notification::make()
                                ->danger()
                                ->title(__('messages.flash.charge_category_not_found'))
                                ->send();
                        }

                        $record->delete();
                        return Notification::make()
                            ->success()
                            ->title(__('messages.flash.charge_category_deleted'))
                            ->send();
                    }),
            ])
            ->recordAction(null)
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
            'index' => Pages\ManageChargeCategories::route('/'),
        ];
    }
}
