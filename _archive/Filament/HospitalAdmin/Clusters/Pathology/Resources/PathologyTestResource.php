<?php

namespace App\Filament\HospitalAdmin\Clusters\Pathology\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Charge;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Models\PathologyTest;
use App\Models\PathologyUnit;
use App\Models\ChargeCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\FrameDecorator\Text;
use Filament\Resources\Resource;
use App\Models\PathologyCategory;
use App\Models\PathologyParameter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use App\Models\PathologyParameterItem;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use App\Repositories\PatientRepository;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\PathologyTestRepository;
use App\Filament\HospitalAdmin\Clusters\Pathology;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use App\Filament\HospitalAdmin\Clusters\Pathology\Resources\PathologyTestResource\Pages;
use App\Filament\HospitalAdmin\Clusters\Pathology\Resources\PathologyTestResource\RelationManagers;

class PathologyTestResource extends Resource
{
    protected static ?string $model = PathologyTest::class;

    protected static ?string $cluster = Pathology::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist'])  && !getModuleAccess('Pathology Tests')) {
            return false;
        } elseif (!auth()->user()->hasRole(['Admin', 'Receptionist']) && !getModuleAccess('Pathology Tests')) {
            return false;
        }
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.pathology_tests');
    }

    public static function getLabel(): string
    {
        return __('messages.pathology_tests');
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
                            ->label(__('messages.case.patient') . ':')
                            ->placeholder(__('messages.document.select_patient'))
                            ->required()
                            ->options(app(PatientRepository::class)->getPatients())
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' .__('messages.case.patient') . ' ' . __('messages.fields.required'),
                            ]),
                        TextInput::make('test_name')
                            ->label(__('messages.pathology_test.test_name') . ':')
                            ->placeholder(__('messages.pathology_test.test_name'))
                            ->required()
                            ->validationAttribute(__('messages.pathology_test.test_name'))
                            ->maxLength(255),
                        TextInput::make('short_name')
                            ->label(__('messages.pathology_test.short_name') . ':')
                            ->placeholder(__('messages.pathology_test.short_name'))
                            ->required()
                            ->validationAttribute(__('messages.pathology_test.short_name'))
                            ->maxLength(255),
                        TextInput::make('test_type')
                            ->label(__('messages.pathology_test.test_type') . ':')
                            ->placeholder(__('messages.pathology_test.test_type'))
                            ->required()
                            ->validationAttribute(__('messages.pathology_test.test_type'))
                            ->maxLength(255),
                        Select::make('category_id')
                            ->label(__('messages.pathology_test.category_name') . ':')
                            ->placeholder(__('messages.medicine.select_category'))
                            ->required()
                            ->options(PathologyCategory::where('tenant_id', getLoggedInUser()->tenant_id)->pluck('name', 'id'))
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' .__('messages.pathology_test.category_name') . ' ' . __('messages.fields.required'),
                            ]),
                        TextInput::make('unit')
                            ->label(__('messages.pathology_test.unit') . ':')
                            ->placeholder(__('messages.pathology_test.unit'))
                            ->numeric()
                            ->minValue(1)
                            ->maxLength(255),
                        TextInput::make('subcategory')
                            ->label(__('messages.pathology_test.subcategory') . ':')
                            ->placeholder(__('messages.pathology_test.subcategory'))
                            ->maxLength(255),
                        TextInput::make('method')
                            ->label(__('messages.pathology_test.method') . ':')
                            ->placeholder(__('messages.pathology_test.method'))
                            ->maxLength(255),
                        TextInput::make('report_days')
                            ->label(__('messages.pathology_test.report_days') . ':')
                            ->placeholder(__('messages.pathology_test.report_days'))
                            ->numeric()
                            ->minValue(1),
                        Select::make('charge_category_id')
                            ->live()
                            ->label(__('messages.pathology_test.charge_category') . ':')
                            ->placeholder(__('messages.pathology_category.select_charge_category'))
                            ->required()
                            ->options(ChargeCategory::where('tenant_id', getLoggedInUser()->tenant_id)->pluck('name', 'id'))
                            ->afterStateUpdated(function ($set, $get) {
                                $id = $get('charge_category_id');
                                $standard_charge = Charge::where('charge_category_id', $id)->value('standard_charge');
                                $set('standard_charge', $standard_charge);
                            })
                            ->searchable()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' .__('messages.pathology_test.charge_category') . ' ' . __('messages.fields.required'),
                            ]),
                        TextInput::make('standard_charge')
                            ->label(__('messages.pathology_test.standard_charge') . ': (Tk)')
                            ->placeholder(__('messages.pathology_test.standard_charge'))
                            ->required()
                            ->validationAttribute(__('messages.pathology_test.standard_charge'))
                            ->readOnly(),
                    ])->columns(4),

                Group::make()->schema([
                    Repeater::make('parameter')->schema([
                        Select::make('parameter_id')
                            ->label(__('messages.new_change.parameter_name'))
                            ->placeholder(__('messages.new_change.select_parameter_name'))
                            ->required()
                            ->options(PathologyParameter::where('tenant_id', getLoggedInUser()->tenant_id)->pluck('parameter_name', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($set, $get) {
                                self::fieldData($get, $set);
                            })
                            ->native(false)
                            ->preload()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' .__('messages.new_change.parameter_name') . ' ' . __('messages.fields.required'),
                            ]),
                        TextInput::make('patient_result')
                            ->label(__('messages.new_change.patient_result'))
                            ->placeholder(__('messages.new_change.patient_result'))
                            ->validationAttribute(__('messages.new_change.patient_result'))
                            ->required(),
                        TextInput::make('reference_range')
                            ->label(__('messages.new_change.reference_range'))
                            ->placeholder(__('messages.new_change.reference_range'))
                            ->readOnly()
                            ->validationAttribute(__('messages.new_change.reference_range'))
                            ->required(),
                        TextInput::make('unit_id')
                            ->label(__('messages.pathology_test.unit'))
                            ->placeholder(__('messages.pathology_test.unit'))
                            ->readOnly()
                            ->validationAttribute(__('messages.pathology_test.unit'))
                            ->required()
                    ])->addActionLabel('Add')->columns(4),
                ])->columnSpanFull(),
            ])->columns(4);
    }

    public static function fieldData($get, $set): void
    {
        if ($get('parameter_id')) {
            $unit = PathologyParameter::where('id', $get('parameter_id'))->value('unit_id');
            $set('reference_range', PathologyParameter::where('id', $get('parameter_id'))->value('reference_range'));
            $set('unit_id', PathologyUnit::where('id', $unit)->value('name'));
        }
    }

    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole(['Admin','Receptionist','Pharmacist','Lab Technician']) && !getModuleAccess('Pathology Tests')) {
            abort(404);
        }

        $table = $table->modifyQueryUsing(function ($query) {
            return $query->where('tenant_id', getLoggedInUser()->tenant_id);
        });
        return $table
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('test_name')
                    ->label(__('messages.pathology_test.test_name'))
                    ->searchable()
                    ->sortable(),
                SpatieMediaLibraryImageColumn::make('patient.user.profile')
                    ->label(__('messages.case.patient'))
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        if (!empty($record->patient->user) && !$record->patient->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->patient->user->full_name);
                        }
                    })
                    ->sortable(['first_name'])
                    ->collection('profile')
                    ->width(50)->height(50),
                TextColumn::make('patient.user.full_name')
                    ->label('')
                    ->description(function ($record) {
                        if (empty($record->patient->user)) {
                            return __('messages.common.n/a');
                        }
                        return $record->patient->user->email;
                    })
                    ->html()
                    ->formatStateUsing(fn($record) => '<a href="' . PatientResource::getUrl('view', ['record' => $record->patient->id]) . '"class="hoverLink">' . $record->patient->user->full_name . '</a>')
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->searchable(['first_name', 'last_name', 'email']),
                TextColumn::make('short_name')
                    ->label(__('messages.pathology_test.short_name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('test_type')
                    ->label(__('messages.pathology_test.test_type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('pathologycategory.name')
                    ->label(__('messages.pathology_test.category_name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('chargecategory.name')
                    ->label(__('messages.pathology_test.charge_category'))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                TableAction::make('pdf')
                    ->iconButton()
                    ->icon('heroicon-s-printer')
                    ->color('warning')
                    ->url(function ($record) {
                        return route('pathology.test.pdf', $record->id);
                    })
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()->iconButton()->successNotificationTitle(__('messages.new_change.pathology_unit') . ' ' . __('messages.common.updated_successfully')),
                Tables\Actions\DeleteAction::make()->iconButton()->action(function ($record) {
                    if (!canAccessRecord($record, $record->id)) {
                        return  Notification::make()
                            ->title(__('messages.flash.pathology_test_not_found'))
                            ->danger()
                            ->send();
                    }
                    $record->parameterItems()->delete();
                    $record->delete();

                    return Notification::make()
                        ->title(__('messages.flash.pathology_test_deleted'))
                        ->success()
                        ->send();
                })
            ])
            ->recordUrl(null)
            ->actionsColumnLabel(__('messages.common.action'))
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
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
            'index' => Pages\ListPathologyTests::route('/'),
            'create' => Pages\CreatePathologyTest::route('/create'),
            'view' => Pages\ViewPathologyTest::route('/{record}'),
            'edit' => Pages\EditPathologyTest::route('/{record}/edit'),
        ];
    }
}
