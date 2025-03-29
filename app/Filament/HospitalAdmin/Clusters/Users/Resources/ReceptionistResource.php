<?php

namespace App\Filament\HospitalAdmin\Clusters\Users\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Receptionist;
use App\Models\EmployeePayroll;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\TextEntry;
use App\Filament\HospitalAdmin\Clusters\Users;
use Illuminate\Contracts\Database\Query\Builder;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use App\Livewire\ReceptionistPayrollRelationTable;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneInputColumn;
use Filament\Infolists\Components\Group as InfolistGroup;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use App\Filament\HospitalAdmin\Clusters\Users\Resources\ReceptionistResource\Pages;
use App\Filament\HospitalAdmin\Clusters\Users\Resources\ReceptionistResource\RelationManagers\PayrollsRelationManager;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class ReceptionistResource extends Resource
{
    protected static ?string $model = Receptionist::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Users::class;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole('Admin') && !getModuleAccess('Receptionists')) {
            return false;
        }
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.receptionists');
    }

    public static function getLabel(): string
    {
        return __('messages.receptionists');
    }


    public static function canCreate(): bool
    {
        if (auth()->user()->hasRole('Admin') && getModuleAccess('Receptionists')) {
            return true;
        }
        return false;
    }
    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->hasRole('Admin') && getModuleAccess('Receptionists')) {
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (auth()->user()->hasRole('Admin') && getModuleAccess('Receptionists')) {
            return true;
        }
        return false;
    }

    public static function canViewAny(): bool
    {
        if (auth()->user()->hasRole('Admin')) {
            return true;
        }
        return false;
    }

    public static function form(Form $form): Form
    {
        if ($form->getOperation() === 'edit') {
            $receptionistData = $form->model;
            $form->model = User::find($receptionistData->user_id);
        }
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('messages.user.first_name') . ':')
                            ->placeholder(__('messages.user.first_name'))
                            ->validationAttribute(__('messages.user.first_name'))
                            ->required()
                            ->maxLength(500),
                        TextInput::make('last_name')
                            ->label(__('messages.user.last_name') . ':')
                            ->placeholder(__('messages.user.last_name'))
                            ->validationAttribute(__('messages.user.last_name'))
                            ->required()
                            ->maxLength(500),
                        TextInput::make('email')
                            ->unique('users', 'email', ignoreRecord: true)
                            ->label(__('messages.user.email') . ':')
                            ->placeholder(__('messages.user.email'))
                            ->validationAttribute(__('messages.user.email'))
                            ->email()
                            ->validationMessages([
                                'unique' => __('messages.user.email') . ' ' . __('messages.common.is_already_exists'),
                            ])
                            ->required(),
                        TextInput::make('designation')
                            ->label(__('messages.user.designation') . ': ')
                            ->validationAttribute(__('messages.user.designation'))
                            ->required()
                            ->placeholder(__('messages.user.designation')),
                        PhoneInput::make('phone')
                            ->defaultCountry('IN')
                            ->rules(function (Get $get) {
                                return [
                                    'required',
                                    'phone:AUTO,' . strtoupper($get('prefix_code')),
                                ];
                            })
                            ->countryStatePath('region_code')
                            ->validationMessages([
                                'phone' => __('messages.common.invalid_number'),
                            ])
                            ->afterStateHydrated(function ($component, $record, $operation) {
                                if ($operation == 'edit') {
                                    if (!empty($record->phone)) {
                                        $phoneNumber = (empty($record->region_code) ? '+' : $record->region_code) . getPhoneNumber($record->phone);
                                    } else {
                                        $phoneNumber = null;
                                    }
                                    $component->state($phoneNumber);
                                }
                            })
                            ->label(__('messages.user.phone') . ':'),
                        Hidden::make('region_code'),
                        Group::make()->schema([
                            Radio::make('gender')
                                ->label(__('messages.user.gender') . ':')
                                ->validationAttribute(__('messages.user.gender'))
                                ->required()
                                ->options([
                                    '0' => __('messages.user.male'),
                                    '1' => __('messages.user.female'),
                                ])
                                ->columns(2),
                            Toggle::make('status')
                                ->default(1)
                                ->label(__('messages.user.status') . ':')
                                ->inline(false),
                        ])->columns(2),
                        TextInput::make('qualification')
                            ->label(__('messages.user.qualification') . ':')
                            ->placeholder(__('messages.user.qualification'))
                            ->validationAttribute(__('messages.user.qualification'))
                            ->required(),
                        DatePicker::make('ob')
                            ->native(false)
                            ->maxDate(today())
                            ->label(__('messages.user.dob') . ':'),
                        Select::make('blood_group')
                            ->label(__('messages.user.blood_group') . ':')
                            ->options(getBloodGroups())
                            ->native(false),
                        Group::make()->schema([
                            Forms\Components\TextInput::make('password')
                                ->revealable()
                                ->visible(function (?string $operation) {
                                    return $operation == 'create';
                                })
                                ->rules(['min:8', 'max:20'])
                                ->confirmed()
                                ->label(__('messages.user.password') . ':')
                                ->placeholder(__('messages.user.password'))
                                ->validationAttribute(__('messages.user.password'))
                                ->required()
                                ->password()
                                ->maxLength(20),

                            TextInput::make('password_confirmation')
                                ->dehydrated(false)
                                ->visible(function (?string $operation) {
                                    return $operation == 'create';
                                })
                                ->label(__('messages.user.password_confirmation') . ':')
                                ->placeholder(__('messages.user.password_confirmation'))
                                ->validationAttribute(__('messages.user.password_confirmation'))
                                ->revealable()
                                ->required()
                                ->password()
                                ->maxLength(20),
                        ])->columns(2),
                        SpatieMediaLibraryFileUpload::make('user.profile')
                            ->label(__('messages.common.profile') . ':')
                            ->avatar()
                            ->disk(config('app.media_disk'))
                            ->collection(User::COLLECTION_PROFILE_PICTURES),
                    ])->columns(2),

                Section::make(__('messages.user.address_details'))
                    ->schema([
                        TextInput::make('address1')
                            ->label(__('messages.user.address1') . ':')
                            ->placeholder(__('messages.user.address1')),
                        TextInput::make('address2')
                            ->label(__('messages.user.address2') . ':')
                            ->placeholder(__('messages.user.address2')),
                        Group::make()->schema([
                            TextInput::make('city')
                                ->label(__('messages.user.city') . ':')
                                ->placeholder(__('messages.user.city')),
                            TextInput::make('zip')
                                ->label(__('messages.user.zip') . ':')
                                ->placeholder(__('messages.user.zip')),
                        ])->columns(2),

                    ])->columns(2),
            ]);
    }
    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole('Admin') && !getModuleAccess('Receptionists')) {
            abort(404);
        }

        $table = $table->modifyQueryUsing(function (Builder $query) {
            $query->with('user')->whereTenantId(auth()->user()->tenant_id);
            return $query;
        });
        return $table
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('user.profile')
                    ->collection(User::COLLECTION_PROFILE_PICTURES)
                    ->rounded()
                    ->defaultImageUrl(function ($record) {
                        if (!$record->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->user->full_name);
                        }
                    })
                    ->sortable(['first_name'])
                    ->label(__('messages.receptionists'))
                    ->width(50)
                    ->height(50),

                TextColumn::make('user.full_name')
                    ->label('')
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->description(function (Receptionist $record) {
                        $user = User::find($record->user_id);
                        return $user->email;
                    })
                    ->searchable(['first_name', 'last_name', 'email']),

                TextColumn::make('user.designation')
                    ->label(__('messages.user.designation'))
                    ->sortable()
                    ->default(__('messages.common.n/a'))
                    ->searchable(),
                PhoneColumn::make('user.phone')
                    ->default(__('messages.common.n/a'))
                    ->formatStateUsing(function ($state, $record) {
                        if (str_starts_with($state, '+') && strlen($state) > 4) {
                            return $state;
                        }
                        if (empty($record->user->phone)) {
                            return __('messages.common.n/a');
                        }

                        return $record->user->region_code . $record->user->phone;
                    })
                    ->label(__('messages.user.phone')),
                ToggleColumn::make('user.status')
                    ->label(__('messages.user.status'))
                    ->updateStateUsing(function (Receptionist $record, bool $state) {
                        $user = User::find($record->user_id);
                        if ($state) {
                            $user->status = 1;
                            $user->save();
                        } else {
                            $user->status = 0;
                            $user->save();
                        }
                        Notification::make()
                            ->title(__('messages.common.status_updated_successfully'))
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                Filter::make(__('messages.user.status'))
                    ->form([
                        Select::make(__('messages.user.status'))
                            ->options([
                                'All' => __('messages.filter.all'),
                                1 => __('messages.filter.active'),
                                0 => __('messsa.filter.inactive'),
                            ])->default('All')->native(false)
                            ->label(__('messages.user.status') . ':'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data[__('messages.common.status')] == 'All') {
                            $query->with('user');
                        }
                        if ($data[__('messages.common.status')] == 1) {
                            $query->with('user')->whereHas('user', fn(Builder $query) => $query->where('status', 1));
                        } elseif ($data[__('messages.common.status')] == 0) {
                            $query->with('user')->whereHas('user', fn(Builder $query) => $query->where('status', 0));
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('info')->iconButton()->extraAttributes(['class' => 'hidden'])->action(function ($data, $record) {
                    if (!canAccessRecord($record, $record->id)) {
                        return Notification::make()
                            ->title(__('messages.flash.not_allow_access_record'))
                            ->danger()
                            ->send();
                    }
                }),
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton()
                    ->successNotificationTitle(__('messages.flash.receptionist_deleted_successfully'))
                    ->action(function ($data, $record) {
                        $accountant = Receptionist::find($record->id);
                        if (!canAccessRecord(Receptionist::class, $accountant->id)) {
                            return Notification::make()
                                ->title(__('messages.flash.receptionist_cant_deleted'))
                                ->danger()
                                ->send();
                        }

                        if (getLoggedInUser()->is_default == 1) {
                            return Notification::make()
                                ->title(__('messages.common.this_action_is_not_allowed_for_default_record'))
                                ->danger()
                                ->send();
                        }

                        $empPayRollResult = canDeletePayroll(
                            EmployeePayroll::class,
                            'owner_id',
                            $accountant->id,
                            $accountant->user->owner_type
                        );

                        if ($empPayRollResult) {
                            return Notification::make()
                                ->title(__('messages.flash.receptionist_cant_deleted'))
                                ->danger()
                                ->send();
                        }
                        $accountant->user()->delete();
                        $accountant->address()->delete();
                        $accountant->delete();
                        return Notification::make()
                            ->title(__('messages.flash.receptionist_deleted'))
                            ->success()
                            ->send();
                    }),
            ])->actionsColumnLabel(__('messages.common.action'))
            ->bulkActions([])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make()->schema([
                    SpatieMediaLibraryImageEntry::make('user.profile')->collection(User::COLLECTION_PROFILE_PICTURES)->label("")->columnSpan(2)->width(100)->height(100)->defaultImageUrl(function ($record) {
                        if (!$record->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->user->full_name);
                        }
                    })->circular()->columnSpan(1),
                    InfolistGroup::make([
                        TextEntry::make('user.status')
                            ->label('')
                            ->formatStateUsing(fn($state) => $state ? __('messages.common.active') : __('messages.common.deactive'))
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'danger')
                            ->columnSpan(1),
                        TextEntry::make('user.full_name')
                            ->label('')
                            ->extraAttributes(['class' => 'font-black'])
                            ->color('primary')
                            ->columnSpan(1),
                        TextEntry::make('user.email')
                            ->label('')
                            ->icon('fas-envelope')
                            ->formatStateUsing(fn($state, $record) => "<a href='mailto:{$state}'>{$state}</a>")
                            ->html()
                            ->columnSpan(1),

                    ]),
                    TextEntry::make('user.address')
                        ->label('')
                        ->icon('fas-location-dot')
                        ->getStateUsing(function ($record) {
                            if (!empty($record->address->address1) && !empty($record->address->address2)) {
                                return $record->address->address1 . ', ' . $record->address->address2;
                            } elseif (!empty($record->address->address1)) {
                                return $record->address->address1;
                            } elseif (!empty($record->address->address2)) {
                                return $record->address->address2;
                            } else {
                                return __('messages.common.n/a');
                            }
                        })
                        ->html()->columnSpan(3),
                ])->columns(10),
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make(__('messages.overview'))->schema([
                            PhoneEntry::make('user.phone')
                                ->label(__('messages.user.phone') . ':')
                                ->default(__('messages.common.n/a'))
                                ->formatStateUsing(function ($state, $record) {
                                    if (str_starts_with($state, '+') && strlen($state) > 4) {
                                        return $state;
                                    }
                                    if (empty($record->user->phone)) {
                                        return __('messages.common.n/a');
                                    }

                                    return $record->user->region_code . $record->user->phone;
                                }),
                            TextEntry::make('user.gender')
                                ->label(__('messages.user.gender') . ':')
                                ->getStateUsing(fn($record) => $record->user->gender == 0 ? __('messages.user.male') : __('messages.user.female')),
                            TextEntry::make('user.blood_group')
                                ->label(__('messages.user.blood_group') . ':')
                                ->getStateUsing(fn($record) => $record->user->blood_group ?? __('messages.common.n/a')),
                            TextEntry::make('user.dob')
                                ->label(__('messages.user.dob') . ':')
                                ->getStateUsing(fn($record) => $record->user->dob ? Carbon::parse($record->user->dob)->translatedFormat('jS M, Y') : __('messages.common.n/a')),
                            TextEntry::make('user.designation')
                                ->label(__('messages.user.designation') . ':')
                                ->getStateUsing(fn($record) => $record->user->designation ?? __('messages.common.n/a')),
                            TextEntry::make('user.qualification')
                                ->label(__('messages.user.qualification') . ':')
                                ->getStateUsing(fn($record) => $record->user->qualification ?? __('messages.common.n/a')),
                            TextEntry::make('created_at')
                                ->label(__('messages.common.created_at') . ':')
                                ->getStateUsing(fn($record) => $record->user->created_at->diffForHumans()),
                            TextEntry::make('updated_at')
                                ->label(__('messages.common.last_updated') . ':')
                                ->getStateUsing(fn($record) => $record->user->updated_at->diffForHumans()),
                        ])->columns(2),
                        Tabs\Tab::make(__('messages.my_payrolls'))->schema([
                            Livewire::make(ReceptionistPayrollRelationTable::class)
                        ])
                    ])->columnSpanFull(),
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
            'index' => Pages\ListReceptionists::route('/'),
            'create' => Pages\CreateReceptionist::route('/create'),
            'view' => Pages\ViewReceptionist::route('/{record}'),
            'edit' => Pages\EditReceptionist::route('/{record}/edit'),
        ];
    }
}
