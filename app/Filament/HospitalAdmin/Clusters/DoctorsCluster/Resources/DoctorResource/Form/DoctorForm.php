<?php

   


    namespace App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\Form;

    use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource;

    use Filament\Forms;
    use Filament\Forms\Components\DatePicker;
    use Filament\Forms\Components\Fieldset;
    use Filament\Forms\Components\Group;
    use Filament\Forms\Components\Hidden;
    use Filament\Forms\Components\Radio;
    use Filament\Forms\Components\Section;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Components\Toggle;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
    use App\Models\DoctorDepartment;
    use App\Models\User;
    use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

    class DoctorForm
    {
        public static function schema(): array
        {
            return [
                Section::make()
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->validationAttribute(__('messages.user.first_name'))
                            ->label(__('messages.user.first_name') . ':')
                            ->placeholder(__('messages.user.first_name'))
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->required()
                            ->validationAttribute(__('messages.user.last_name'))
                            ->label(__('messages.user.last_name') . ':')
                            ->placeholder(__('messages.user.last_name'))
                            ->maxLength(255),
                        Select::make('doctor_department_id')
                            ->options(DoctorDepartment::get()->where('tenant_id', getLoggedInUser()->tenant_id)->pluck('title', 'id')->sort())
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->optionsLimit(count(DoctorDepartment::get()->where('tenant_id', getLoggedInUser()->tenant_id)))
                            ->label(__('messages.doctor_department.doctor_department') . ':')
                            ->placeholder(__('messages.doctor_department.doctor_department')),
                        TextInput::make('email')
                            ->unique('users', 'email', ignoreRecord: true)
                            ->label(__('messages.user.email') . ':')
                            ->placeholder(__('messages.user.email'))
                            ->email()
                            ->required(),
                        TextInput::make('designation')
                            ->label(__('messages.user.designation') . ': ')
                            ->required()
                            ->validationAttribute(__('messages.user.designation'))
                            ->placeholder(__('messages.user.designation')),
                        PhoneInput::make('phone')
                            ->defaultCountry('IN')
                            ->rules(function ($get) {
                                return [
                                    'phone:AUTO,' . strtoupper($get('prefix_code')),
                                ];
                            })
                            ->validationMessages([
                                'phone' => __('messages.common.invalid_number'),
                            ])
                            ->afterStateHydrated(function ($component, $record, $operation) {
                                if ($operation == 'edit') {
                                    if (!empty($record->phone)) {
                                        $phoneNumber = (empty($record->region_code || !str_starts_with($record->phone, '+')) ? '+' : $record->region_code) . getPhoneNumber($record->phone);
                                    } else {
                                        $phoneNumber = null;
                                    }
                                    $component->state($phoneNumber);
                                }
                            })
                            ->countryStatePath('region_code')
                            ->label(__('messages.user.phone') . ':'),
                        Hidden::make('region_code'),
                        TextInput::make('qualification')
                            ->label(__('messages.user.qualification') . ':')
                            ->placeholder(__('messages.user.qualification'))
                            ->validationAttribute(__('messages.user.qualification'))
                            ->required(),
                        DatePicker::make('dob')
                            ->native(false)
                            ->maxDate(today())
                            ->label(__('messages.user.dob') . ':'),
                        Select::make('blood_group')
                            ->label(__('messages.user.blood_group') . ':')
                            ->options(getBloodGroups())
                            ->native(false),
                        Group::make()->schema([
                            Radio::make('gender')
                                ->label(__('messages.user.gender') . ':')
                                ->required()
                                ->validationAttribute(__('messages.user.gender'))
                                ->default(0)
                                ->options([
                                    0 => __('messages.user.male'),
                                    1 => __('messages.user.female'),
                                ])->columns(2)->columnSpan(2),
                            Toggle::make('status')
                                ->default(1)
                                ->label(__('messages.user.status') . ':')
                                ->inline(false)
                                ->columnSpan(1),
                        ])->columns(3),
                        TextInput::make('specialist')
                            ->required()
                            ->validationAttribute(__('messages.doctor.specialist'))
                            ->label(__('messages.doctor.specialist') . ':')
                            ->placeholder(__('messages.doctor.specialist')),
                        TextInput::make('appointment_charge')
                            ->label(__('messages.appointment_charge') . ':')
                            ->placeholder(__('messages.appointment_charge'))
                            ->numeric()
                            ->minValue(0),
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
                                ->required()
                                ->validationAttribute(__('messages.user.password'))
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
                        Textarea::make('description')
                            ->label(__('messages.doctor_department.description') . ':')
                            ->rows(4)
                            ->placeholder(__('messages.doctor_department.description')),
                        SpatieMediaLibraryFileUpload::make('user.profile')
                            ->label(__('messages.common.profile') . ':')
                            ->avatar()
                            ->image()
                            ->disk(config('app.media_disk'))
                            ->collection(User::COLLECTION_PROFILE_PICTURES),
                        Fieldset::make('Address Details')
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
                            ]),
                        Fieldset::make(__('messages.setting.social_details'))
                            ->schema([
                                TextInput::make('facebook_url')
                                    ->label(__('messages.facebook_url') . ':')
                                    ->url()
                                    ->placeholder(__('messages.facebook_url')),
                                TextInput::make('twitter_url')
                                    ->label(__('messages.twitter_url') . ':')
                                    ->url()
                                    ->placeholder(__('messages.twitter_url')),
                                TextInput::make('instagram_url')
                                    ->label(__('messages.instagram_url') . ':')
                                    ->url()
                                    ->placeholder(__('messages.instagram_url')),
                                TextInput::make('linkedIn_url')
                                    ->label(__('messages.linkedIn_url') . ':')
                                    ->url()
                                    ->placeholder(__('messages.linkedIn_url')),
                            ])
                    ])->columns(2),
            ];
        }
    }
