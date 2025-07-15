<?php

namespace App\Filament\HospitalAdmin\Clusters\Radiology\Resources;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use Filament\Forms;
use Filament\Tables;
use App\Models\Charge;
use Filament\Forms\Form;
use Mockery\Matcher\Not;
use Filament\Tables\Table;
use App\Models\RadiologyTest;
use App\Models\ChargeCategory;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\RadiologyCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\HospitalAdmin\Clusters\Radiology;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HospitalAdmin\Clusters\Radiology\Resources\RadiologyTestResource\Pages;
use App\Filament\HospitalAdmin\Clusters\Radiology\Resources\RadiologyTestResource\RelationManagers;
use App\Models\User;
use App\Repositories\PatientRepository;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class RadiologyTestResource extends Resource
{
    protected static ?string $model = RadiologyTest::class;

    protected static ?string $cluster = Radiology::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist'])  && !getModuleAccess('Radiology Tests')) {
            return false;
        } elseif (!auth()->user()->hasRole(['Admin', 'Receptionist']) && !getModuleAccess('Radiology Tests')) {
            return false;
        }
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.radiology_tests');
    }

    public static function getLabel(): string
    {
        return __('messages.radiology_tests');
    }

    public static function canCreate(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist', 'Pharmacist', 'Lab Technician'])) {
            return true;
        }
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist', 'Pharmacist', 'Lab Technician'])) {
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist', 'Pharmacist', 'Lab Technician'])) {
            return true;
        }
        return false;
    }

    public static function canViewAny(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist', 'Pharmacist', 'Lab Technician'])) {
            return true;
        }
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('patient_id')
                            ->label(__('messages.role.patient') . ':')
                            ->placeholder(__('messages.document.select_patient'))
                            ->options(app(PatientRepository::class)->getPatients())
                            ->required()
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.pathology_test.test_name') . ' ' . __('messages.fields.required'),
                            ]),
                        TextInput::make('test_name')
                            ->label(__('messages.pathology_test.test_name') . ':')
                            ->placeholder(__('messages.pathology_test.test_name'))
                            ->maxLength(255)
                            ->validationAttribute(__('messages.pathology_test.test_name'))
                            ->required(),
                        TextInput::make('short_name')
                            ->label(__('messages.pathology_test.short_name') . ':')
                            ->placeholder(__('messages.pathology_test.short_name'))
                            ->maxLength(255)
                            ->validationAttribute(__('messages.pathology_test.short_name'))
                            ->required(),
                        TextInput::make('test_type')
                            ->label(__('messages.pathology_test.test_type') . ':')
                            ->placeholder(__('messages.pathology_test.test_type'))
                            ->maxLength(255)
                            ->validationAttribute(__('messages.pathology_test.test_type'))
                            ->required(),
                        Select::make('category_id')
                            ->label(__('messages.radiology_test.category_name') . ':')
                            ->options(RadiologyCategory::where('tenant_id', getLoggedInUser()->tenant_id)->pluck('name', 'id')->sort())
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.radiology_test.category_name') . ' ' . __('messages.fields.required'),
                            ]),
                        TextInput::make('subcategory')
                            ->label(__('messages.radiology_test.subcategory') . ':')
                            ->placeholder(__('messages.radiology_test.subcategory'))
                            ->maxLength(255),
                        TextInput::make('report_days')
                            ->label(__('messages.radiology_test.report_days') . ':')
                            ->placeholder(__('messages.radiology_test.report_days'))
                            ->maxLength(255),
                        Select::make('charge_category_id')
                            ->live()
                            ->label(__('messages.pathology_test.charge_category') . ':')
                            ->placeholder(__('messages.pathology_category.select_charge_category'))
                            ->required()
                            ->options(ChargeCategory::where('tenant_id', getLoggedInUser()->tenant_id)->pluck('name', 'id'))
                            ->afterStateUpdated(function ($set, $get,) {
                                $id = $get('charge_category_id');
                                $charge_id = Charge::where('charge_category_id', $id)->pluck('id')->first();
                                $set('charge_id', $charge_id);
                                if ($charge_id) {
                                    $set('standard_charge', Charge::where('charge_category_id', $id)->value('standard_charge'));
                                }
                            })
                            ->searchable()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.pathology_test.charge_category') . ' ' . __('messages.fields.required'),
                            ]),
                        Select::make('charge_id')
                            ->live()
                            ->label(__('messages.delete.charge') . ':')
                            ->placeholder(__('messages.new_change.select_charge'))
                            ->options(function (callable $get) {
                                $id = $get('charge_category_id');
                                return Charge::where('charge_category_id', $id)->pluck('code', 'id');
                            })
                            // ->disabled(function (callable $get) {
                            //     $id = $get('charge_category_id');
                            //     $charge_id = Charge::where('charge_category_id', $id)->pluck('code', 'id');
                            //     if (!empty($charge_id->toArray())) {
                            //         return false;
                            //     }
                            //     return true;
                            // })
                            ->native(false)
                            ->searchable()
                            ->afterStateUpdated(function ($set, $get, $state) {
                                $id = $get('charge_category_id');
                                $charge_id = Charge::where('charge_category_id', $id)->where('id', $state)
                                    ->value('standard_charge');
                                if ($id && $get('charge_id')) {
                                    $set('standard_charge', $charge_id);
                                }
                            })
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.delete.charge') . ' ' . __('messages.fields.required'),
                            ]),
                        TextInput::make('standard_charge')
                            ->live()
                            ->required()
                            ->validationAttribute(__('messages.radiology_test.standard_charge'))
                            ->readOnly()
                            ->label(function () {
                                if (getCurrencySymbol() != null) {
                                    return __('messages.radiology_test.standard_charge') . ' : ' . '(' . getCurrencySymbol() . ')';
                                }
                                return __('messages.radiology_test.standard_charge') . ':';
                            })
                            ->placeholder(__('messages.radiology_test.standard_charge'))
                            ->readOnly(fn($state) => $state == null ?? true)
                    ])->columns(4),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist', 'Pharmacist', 'Lab Technician']) && !getModuleAccess('Radiology Tests')) {
            abort(404);
        }

        $table = $table->modifyQueryUsing(function ($query) {
            return $query->where('tenant_id', getLoggedInUser()->tenant_id);
        });
        return $table
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('test_name')
                    ->label(__('messages.pathology_test.test_name'))
                    ->getStateUsing(fn($record) => $record->test_name ?? __('messages.common.n/a'))
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                SpatieMediaLibraryImageColumn::make('patient.user.profile')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        if (!empty($record->patient->user) && !$record->patient->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->patient->user->full_name);
                        }
                    })
                    ->collection('profile')
                    ->width(50)->height(50),
                TextColumn::make('patient.user.full_name')
                    ->label(__('messages.case.patient'))
                    ->default(__('messages.common.n/a'))
                    ->description(function ($record) {
                        if (empty($record->patient->user)) {
                            return '';
                        }
                        return $record->patient->user->email;
                    })
                    ->sortable(['first_name'])
                    ->html()
                    ->formatStateUsing(fn($record) => empty($record->patient) ? '<span>' . __('messages.common.n/a') . '</span>'  : '<a href="' . PatientResource::getUrl('view', ['record' => $record->patient->id]) . '"class="hoverLink font-bold">' . $record->patient->user->full_name . '</a>')
                    ->color('primary')
                    ->searchable(['first_name', 'last_name', 'email']),
                Tables\Columns\TextColumn::make('short_name')
                    ->label(__('messages.pathology_test.short_name'))
                    ->getStateUsing(fn($record) => $record->short_name ?? __('messages.common.n/a'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('test_type')
                    ->label(__('messages.pathology_test.test_type'))
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->test_type ?? __('messages.common.n/a'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('category_id')
                    ->label(__('messages.medicine.category'))
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->radiologycategory->name ?? __('messages.common.n/a'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('chargecategory.name')
                    ->label(__('messages.charge.charge_category'))
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->chargecategory->name ?? __('messages.common.n/a'))
                    ->sortable(),
            ])
            ->recordUrl(null)
            // ->recordAction(null)
            ->actions([
                // Tables\Actions\ViewAction::make()->color('info')->iconButton(),
                Tables\Actions\EditAction::make()->iconButton()->action(function ($record) {
                    if (! canAccessRecord($record, $record->id)) {
                        Notification::make()
                            ->title(__('messages.flash.not_allow_access_record'))
                            ->danger()
                            ->send();

                        return Redirect::back();
                    }
                }),
                Tables\Actions\DeleteAction::make()->iconButton()->action(function ($record) {
                    if (! canAccessRecord($record, $record->id)) {
                        return Notification::make()
                            ->title(__('messages.flash.not_allow_access_record'))
                            ->danger()
                            ->send();
                    }

                    $record->delete();

                    return Notification::make()
                        ->title(__('messages.flash.radiology_test_deleted'))
                        ->success()
                        ->send();
                }),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('test_name')
                    ->label(__('messages.pathology_test.test_name') . ':')
                    ->getStateUsing(fn($record) => $record->test_name ?? __('messages.common.n/a')),
                TextEntry::make('short_name')
                    ->label(__('messages.pathology_test.short_name') . ':')
                    ->getStateUsing(fn($record) => $record->short_name ?? __('messages.common.n/a')),
                TextEntry::make('test_type')
                    ->label(__('messages.pathology_test.test_type') . ':')
                    ->getStateUsing(fn($record) => $record->test_type ?? __('messages.common.n/a')),
                TextEntry::make('category_id')
                    ->label(__('messages.radiology_test.category_name') . ':')
                    ->getStateUsing(fn($record) => $record->radiologycategory->name ?? __('messages.common.n/a')),
                TextEntry::make('subcategory')
                    ->label(__('messages.radiology_test.subcategory') . ':')
                    ->getStateUsing(fn($record) => (!empty($record->subcategory)) ? $record->subcategory : __('messages.common.n/a')),
                TextEntry::make('report_days')
                    ->label(__('messages.radiology_test.report_days') . ':')
                    ->getStateUsing(fn($record) => (!empty($record->report_days)) ? $record->report_days : __('messages.common.n/a')),
                TextEntry::make('chargecategory.name')
                    ->label(__('messages.charge.charge_category') . ':')
                    ->default(__('messages.common.n/a')),
                TextEntry::make('standard_charge')
                    ->label(__('messages.radiology_test.standard_charge') . ':')
                    ->default(__('messages.common.n/a'))
                    ->getStateUsing(fn($record) => getCurrencyFormat($record->standard_charge)),
                TextEntry::make('created_at')
                    ->label(__('messages.common.created_at') . ':')
                    ->since(),
                TextEntry::make('updated_at')
                    ->label(__('messages.common.last_updated') . ':')
                    ->since(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRadiologyTests::route('/'),
            'create' => Pages\CreateRadiologyTest::route('/create'),
            'edit' => Pages\EditRadiologyTest::route('/{record}/edit'),
        ];
    }
}
