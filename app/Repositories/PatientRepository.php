<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Address;
use App\Models\Patient;
use App\Models\Department;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\Receptionist;
use App\Models\Subscription;
use PhpParser\Node\Stmt\Nop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification as FilamentNotification;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Enums\Gender;

/**
 * Class PatientRepository
 *
 * @version February 14, 2020, 5:53 am UTC
 */
class PatientRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
    ];

    /**
     * Return searchable fields
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Patient::class;
    }

    /**
     * Merge nested relation payloads (e.g. Filament relationship inputs) into the root array.
     */
    protected function normalizeInput(array $input): array
    {
        if (isset($input['user']) && is_array($input['user'])) {
            $input = array_merge($input, $input['user']);
            unset($input['user']);
        }

        foreach ($input as $key => $value) {
            if (is_string($key) && str_starts_with($key, 'user.')) {
                $attribute = substr($key, 5);
                $input[$attribute] = $value;
                unset($input[$key]);
            }
        }

        if (! array_key_exists('gender', $input)) {
            $requestPayloads = [
                request()->input('user'),
                data_get(request()->all(), 'data.user'),
                data_get(request()->all(), 'serverMemo.data.data.user'),
                data_get(request()->all(), 'serverMemo.data.state.user'),
            ];

            foreach ($requestPayloads as $payload) {
                if (is_array($payload) && ! empty($payload)) {
                    foreach ($payload as $key => $value) {
                        if (! array_key_exists($key, $input)) {
                            $input[$key] = $value;
                        }
                    }
                }
            }

            if (! array_key_exists('gender', $input)) {
                $flattened = Arr::dot(request()->all());
                foreach ($flattened as $key => $value) {
                    if (! is_string($key) || $value === null) {
                        continue;
                    }
                    $pos = strpos($key, 'user.');
                    if ($pos === false) {
                        continue;
                    }
                    $attribute = substr($key, $pos + 5);
                    if ($attribute === '' || str_contains($attribute, '.')) {
                        continue;
                    }
                    if (! array_key_exists($attribute, $input)) {
                        $input[$attribute] = $value;
                    }
                }
            }

            if (! array_key_exists('gender', $input)) {
                $components = request()->input('components', []);
                foreach ($components as $component) {
                    if (! isset($component['snapshot'])) {
                        continue;
                    }

                    $snapshot = json_decode($component['snapshot'], true);
                    if (json_last_error() !== JSON_ERROR_NONE || ! is_array($snapshot)) {
                        continue;
                    }

                    $dataEntries = data_get($snapshot, 'data.data', []);
                    foreach ($dataEntries as $entry) {
                        if (! isset($entry['user']) || ! is_array($entry['user'])) {
                            continue;
                        }
                        foreach ($entry['user'] as $userEntry) {
                            if (! is_array($userEntry)) {
                                continue;
                            }
                            foreach ($userEntry as $key => $value) {
                                if (! array_key_exists($key, $input)) {
                                    $input[$key] = $value;
                                }
                            }
                            break 2;
                        }
                    }
                }
            }
        }

        return $input;
    }

    protected function generateInternalEmail(?string $tenantId = null): string
    {
        $tenantSegment = $tenantId ? Str::slug($tenantId) : 'app';

        return sprintf('patient_%s_%s@patients.local', $tenantSegment, Str::uuid());
    }

    public function store(array $input, bool $mail = true)
{
    try {
        $input = $this->normalizeInput($input);
        // Department por defecto para pacientes
        $departmentId = Department::where('name', 'Patient')->value('id') ?? 3;

        // Password y email por defecto si no se envían
        if (empty($input['password'])) {
            $generatedPassword = Str::random(10);
            $input['password'] = Hash::make($generatedPassword);
            $input['plain_password'] = $generatedPassword;
        } else {
            $input['password'] = Hash::make($input['password']);
        }

        if (empty($input['email'])) {
            $input['email'] = $this->generateInternalEmail($tenantId);
        }

        // Idioma por defecto
        if (!empty(getSuperAdminSettingValue()['default_language']->value)) {
            $input['language'] = getSuperAdminSettingValue()['default_language']->value;
        }

        $tenantId = $input['tenant_id'] ?? getLoggedInUser()?->tenant_id;

        $dob = null;
        if (!empty($rawDob = $input['dob'] ?? $input['birth_date'] ?? null)) {
            $dob = Carbon::parse($rawDob)->format('Y-m-d');
        }
        //dd($input);

        // ==========================
        // 1️⃣ Datos para USERS
        // ==========================
        $userData = [
            'first_name'                  => $input['first_name'] ?? '',
            'last_name'                   => $input['last_name'] ?? '',
            'phone'                       => $input['phone'] ?? null,
            'email'                       => $input['email'],
            'password'                    => $input['password'],
            'department_id'               => $departmentId,
            'gender'                      => isset($input['gender']) ? (int) $input['gender'] : null,
            'tenant_id'                   => $tenantId,
            'status'                      => $input['status'] ?? 1,
            'language'                    => $input['language'] ?? 'es',
            'rips_identification_type_id' => $input['rips_identification_type_id'] ?? null,
            'rips_identification_number'  => $input['rips_identification_number'] ?? null,
            'dob'                         => $dob,
        ];

        $user = User::create($userData);

        if ($mail) {
            $user->sendEmailVerificationNotification();
        }

        // ==========================
        // 2️⃣ Campos personalizados
        // ==========================
        $jsonFields = [];
        foreach ($input as $key => $value) {
            if (strpos($key, 'field') === 0) {
                $jsonFields[$key] = $value;
            }
        }

        // ==========================
        // 3️⃣ Datos para PATIENTS
        // ==========================
        $patientData = [
            'user_id'             => $user->id,
            'tenant_id'           => $tenantId,
            'patient_unique_id'   => strtoupper(Patient::generateUniquePatientId()),
            'custom_field'        => !empty($jsonFields) ? json_encode($jsonFields) : null,
            'record_number'       => $input['record_number'] ?? null,
            'affiliate_number'    => $input['affiliate_number'] ?? null,
            'template_id'         => $input['template_id'] ?? null,
            'type_id'              => $input['type_id'] ?? null,
            'rips_country_id'      => $input['rips_country_id'] ?? null,
            'rips_department_id'   => $input['rips_department_id'] ?? null,
            'rips_municipality_id' => $input['rips_municipality_id'] ?? null,
            'zone_code'            => $input['zone_code'] ?? null,
            'country_of_origin_id' => $input['country_of_origin_id'] ?? null,
            'contact_email'       => $input['contact_email'] ?? null,
            'marital_status_id'   => $input['marital_status_id'] ?? null,
            'birth_place'         => $input['birth_place'] ?? null,
            'residence_address'   => $input['residence_address'] ?? null,
            'occupation'          => $input['occupation'] ?? null,
            'ethnicity'           => $input['ethnicity'] ?? null,
            'education_level'     => $input['education_level'] ?? null,
            'phone_secondary'     => $input['phone_secondary'] ?? null,
            'responsible_name'    => $input['responsible_name'] ?? null,
            'responsible_phone'   => $input['responsible_phone'] ?? null,
            'responsible_relationship' => $input['responsible_relationship'] ?? null,
            'emergency_contact_name'   => $input['emergency_contact_name'] ?? null,
            'emergency_contact_phone'  => $input['emergency_contact_phone'] ?? null,
        ];

        $patient = Patient::create($patientData);

        // ==========================
        // 4️⃣ Dirección
        // ==========================
        if (!empty($address = Address::prepareAddressArray($input))) {
            Address::create(array_merge($address, [
                'owner_id'   => $patient->id,
                'owner_type' => Patient::class,
            ]));
        }

        // ==========================
        // 5️⃣ Rol y relación en User
        // ==========================
        $user->update([
            'owner_id'   => $patient->id,
            'owner_type' => Patient::class,
        ]);
        $user->assignRole($departmentId);

        return $patient;

    } catch (Exception $e) {
        \Log::error('Excepción al crear paciente', ['exception' => $e]);
        throw new \RuntimeException('Error creando el paciente: ' . $e->getMessage(), previous: $e);
    }
}


    public function store2(array $input, bool $mail = true)
    {
        try {
            $input = $this->normalizeInput($input);
            $rawDob = $input['dob'] ?? $input['birth_date'] ?? null;
            $input['dob'] = !empty($rawDob) ? Carbon::parse($rawDob)->format('Y-m-d') : null;

            // $input['phone'] = preparePhoneNumber($input, 'phone');
            //$input['department_id'] = Department::whereName('Patient')->first()->id;
            $input['department_id'] = Department::where('name', 'Patient')->value('id') ?? 3;
            // Asegúrate de que estos campos estén en el $input para User::create()
            $input['gender'] = isset($input['gender']) ? (int) $input['gender'] : null;

            $input['rips_identification_type_id'] = $input['rips_identification_type_id'] ?? null;
            $input['rips_identification_number'] = $input['rips_identification_number'] ?? null;
            if (!isset($input['password']) || empty($input['password'])) {
                // Generar una contraseña aleatoria segura si no se envía una
                $defaultPassword = Str::random(10); // o algo como: 'Password123*'
                $input['password'] = Hash::make($defaultPassword);
                // Puedes almacenar la versión sin hash si necesitas mostrarla o enviarla por email
                $input['plain_password'] = $defaultPassword;
            } else {
                $input['password'] = Hash::make($input['password']);
            }
            if (!isset($input['email']) || empty($input['email'])) {
                $input['email'] = $this->generateInternalEmail($input['tenant_id'] ?? null);
            }


            $input['password'] = Hash::make($input['password']);
            if (!empty(getSuperAdminSettingValue()['default_language']->value)) {
                $input['language'] = getSuperAdminSettingValue()['default_language']->value;
            }
            $input['tenant_id'] = $input['tenant_id'] ?? getLoggedInUser()?->tenant_id;

            $input['status'] = $input['status'] ?? 1;
            //dd($input);
            $user = User::create($input);

            if ($mail) {
                $user->sendEmailVerificationNotification();
            }

            // if (isset($input['image']) && !empty($input['image'])) {
            //     $mediaId = storeProfileImage($user, $input['image']);
            // }
            $jsonFields = [];

            foreach ($input as $key => $value) {
                if (strpos($key, 'field') === 0) {
                    $jsonFields[$key] = $value;
                }
            }
            $patientData = [
                'user_id' => $user->id,
                'patient_unique_id' => strtoupper(Patient::generateUniquePatientId()),
                'custom_field' => !empty($jsonFields) ? json_encode($jsonFields) : null,
            
                // Nuevos campos que estaban en el formulario pero no se guardaban
                'record_number' => $input['record_number'] ?? null,
                'affiliate_number' => $input['affiliate_number'] ?? null,
                'template_id' => $input['template_id'] ?? null,
                //'document_type' => $input['document_type'] ?? null,
                'rips_identification_type_id' => $input['rips_identification_type_id'] ?? null,
                

            
                // Ya existentes:
                'rips_identification_number' => $input['rips_identification_number'] ?? null,
                'type_id' => $input['type_id'] ?? null,
                //'sex_code' => Gender::from((int) $input['gender'])->sexCode(),
                //'gender' => $input['gender'] ?? null,
                //'rips_identification_type_id' => $input['rips_identification_type_id'] ?? null,
                
                'rips_country_id' => $input['rips_country_id'] ?? null,
                'rips_department_id' => $input['rips_department_id'] ?? null,
                'rips_municipality_id' => $input['rips_municipality_id'] ?? null,
                'zone_code' => $input['zone_code'] ?? null,
                'country_of_origin_id' => $input['country_of_origin_id'] ?? null,
                'contact_email' => $input['contact_email'] ?? null,
                'marital_status_id' => $input['marital_status_id'] ?? null,
                'birth_place' => $input['birth_place'] ?? null,
                'residence_address' => $input['residence_address'] ?? null,
                'occupation' => $input['occupation'] ?? null,
                'ethnicity' => $input['ethnicity'] ?? null,
                'education_level' => $input['education_level'] ?? null,
                'phone_secondary' => $input['phone_secondary'] ?? null,
                'responsible_name' => $input['responsible_name'] ?? null,
                'responsible_phone' => $input['responsible_phone'] ?? null,
                'responsible_relationship' => $input['responsible_relationship'] ?? null,
                'emergency_contact_name' => $input['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $input['emergency_contact_phone'] ?? null,
                
            ];
            
            

            foreach ($patientData as $key => $value) {
                if ($value === null) {
                    logger()->warning("Campo $key es NULL");
                }else {
                    logger()->info("Campo $key tiene valor: $value");
                }
            }
            
            $patient = Patient::create($patientData);

            //$patient = Patient::create(['user_id' => $user->id, 'patient_unique_id' => strtoupper(Patient::generateUniquePatientId()), 'custom_field' => $jsonFields]);

            $ownerId = $patient->id;
            $ownerType = Patient::class;

            /*
            $subscription = [
                'user_id'    => $user->id,
                'start_date' => Carbon::now(),
                'end_date'   => Carbon::now()->addDays(6),
                'status'     => 1,
            ];
            Subscription::create($subscription);
            */

            if (!empty($address = Address::prepareAddressArray($input))) {
                Address::create(array_merge($address, [
                    'owner_id' => $ownerId,
                    'owner_type' => $ownerType,
                ]));
            }
            

            $user->update(['owner_id' => $ownerId, 'owner_type' => $ownerType]);
            $user->assignRole($input['department_id']);

            return $user;
        } catch (Exception $e) {
            // throw new UnprocessableEntityHttpException($e->getMessage());
            /*
            FilamentNotification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
            return null; // Evita el uso de una variable no definida
            */
            \Log::error('Excepción al crear paciente', ['exception' => $e]);

            throw new \RuntimeException('Error creando el paciente: ' . json_encode($e->getMessage()), previous: $e);

        }

        return $user;
    }

    public function update2($patient, $input)
    {
        try {
            $input = $this->normalizeInput($input);
            unset($input['password']);
            $jsonFields = [];

            foreach ($input as $key => $value) {
                if (strpos($key, 'field') === 0) {
                    $jsonFields[$key] = $value;
                }
            }
            $user = User::find($patient->user_id);

            /** @var Patient $patient */
            $input['custom_field'] = !empty($jsonFields) ? $jsonFields : null;
            // $input['phone'] = preparePhoneNumber($input, 'phone');
            if (!empty($rawDob = $input['dob'] ?? $input['birth_date'] ?? null)) {
                $input['dob'] = Carbon::parse($rawDob)->format('Y-m-d');
            } else {
                $input['dob'] = null;
            }
            $patient->user->update($input);
            $patient->update($input);

            if (!empty($patient->address)) {
                if (empty($address = Address::prepareAddressArray($input))) {
                    $patient->address->delete();
                }
                $patient->address->update($input);
            } else {
                if (!empty($address = Address::prepareAddressArray($input)) && empty($patient->address)) {
                    $ownerId = $patient->id;
                    $ownerType = Patient::class;
                    Address::create(array_merge($address, ['owner_id' => $ownerId, 'owner_type' => $ownerType]));
                }
            }
        } catch (Exception $e) {
            // throw new UnprocessableEntityHttpException($e->getMessage());
            FilamentNotification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
        }

        return $patient;
    }

    public function update($patient, $input)
{
    try {
        $input = $this->normalizeInput($input);
        unset($input['password']); // No actualizar password aquí

        // ==========================
        // 1️⃣ Campos personalizados
        // ==========================
        $jsonFields = [];
        foreach ($input as $key => $value) {
            if (strpos($key, 'field') === 0) {
                $jsonFields[$key] = $value;
            }
        }

        /** @var User $user */
        $user = User::findOrFail($patient->user_id);

        $dob = $user->dob;
        if (array_key_exists('dob', $input) || array_key_exists('birth_date', $input)) {
            $rawDob = $input['dob'] ?? $input['birth_date'] ?? null;
            $dob = !empty($rawDob) ? Carbon::parse($rawDob)->format('Y-m-d') : null;
        }

        // ==========================
        // 2️⃣ Datos para USERS
        // ==========================
        $userData = [
            'first_name'                  => $input['first_name'] ?? $user->first_name,
            'last_name'                   => $input['last_name'] ?? $user->last_name,
            'phone'                       => array_key_exists('phone', $input) ? $input['phone'] : $user->phone,
            'email'                       => $input['email'] ?? $user->email,
            'gender'                      => isset($input['gender']) ? (int) $input['gender'] : $user->gender,
            'tenant_id'                   => $input['tenant_id'] ?? $user->tenant_id,
            'status'                      => $input['status'] ?? $user->status,
            'language'                    => $input['language'] ?? $user->language,
            'rips_identification_type_id' => $input['rips_identification_type_id'] ?? $user->rips_identification_type_id,
            'rips_identification_number'  => $input['rips_identification_number'] ?? $user->rips_identification_number,
            'dob'                         => $dob,
        ];
        $user->update($userData);

        // ==========================
        // 3️⃣ Datos para PATIENTS
        // ==========================
        $patientData = [
            'custom_field'        => !empty($jsonFields) ? json_encode($jsonFields) : $patient->custom_field,
            'record_number'       => $input['record_number'] ?? $patient->record_number,
            'affiliate_number'    => $input['affiliate_number'] ?? $patient->affiliate_number,
            'template_id'         => $input['template_id'] ?? $patient->template_id,
            'type_id'              => $input['type_id'] ?? $patient->type_id,
            'rips_country_id'      => $input['rips_country_id'] ?? $patient->rips_country_id,
            'rips_department_id'   => $input['rips_department_id'] ?? $patient->rips_department_id,
            'rips_municipality_id' => $input['rips_municipality_id'] ?? $patient->rips_municipality_id,
            'zone_code'            => $input['zone_code'] ?? $patient->zone_code,
            'country_of_origin_id' => $input['country_of_origin_id'] ?? $patient->country_of_origin_id,
            'contact_email'        => array_key_exists('contact_email', $input) ? $input['contact_email'] : $patient->contact_email,
            'marital_status_id'    => array_key_exists('marital_status_id', $input) ? $input['marital_status_id'] : $patient->marital_status_id,
            'birth_place'          => array_key_exists('birth_place', $input) ? $input['birth_place'] : $patient->birth_place,
            'residence_address'    => array_key_exists('residence_address', $input) ? $input['residence_address'] : $patient->residence_address,
            'occupation'           => array_key_exists('occupation', $input) ? $input['occupation'] : $patient->occupation,
            'ethnicity'            => array_key_exists('ethnicity', $input) ? $input['ethnicity'] : $patient->ethnicity,
            'education_level'      => array_key_exists('education_level', $input) ? $input['education_level'] : $patient->education_level,
            'phone_secondary'      => array_key_exists('phone_secondary', $input) ? $input['phone_secondary'] : $patient->phone_secondary,
            'responsible_name'     => array_key_exists('responsible_name', $input) ? $input['responsible_name'] : $patient->responsible_name,
            'responsible_phone'    => array_key_exists('responsible_phone', $input) ? $input['responsible_phone'] : $patient->responsible_phone,
            'responsible_relationship' => array_key_exists('responsible_relationship', $input) ? $input['responsible_relationship'] : $patient->responsible_relationship,
            'emergency_contact_name'    => array_key_exists('emergency_contact_name', $input) ? $input['emergency_contact_name'] : $patient->emergency_contact_name,
            'emergency_contact_phone'   => array_key_exists('emergency_contact_phone', $input) ? $input['emergency_contact_phone'] : $patient->emergency_contact_phone,
        ];
        $patient->update($patientData);

        // ==========================
        // 4️⃣ Dirección
        // ==========================
        if (!empty($patient->address)) {
            if (empty($address = Address::prepareAddressArray($input))) {
                $patient->address->delete();
            } else {
                $patient->address->update($address);
            }
        } elseif (!empty($address = Address::prepareAddressArray($input))) {
            Address::create(array_merge($address, [
                'owner_id'   => $patient->id,
                'owner_type' => Patient::class,
            ]));
        }

    } catch (Exception $e) {
        FilamentNotification::make()
            ->title($e->getMessage())
            ->danger()
            ->send();
    }

    return $patient;
}


    public function getPatients()
    {
        $user = Auth::user();
        if ($user->hasRole('Doctor')) {
            $patients = getPatientsList($user->owner_id);
        } else {
            $patients = Patient::where('tenant_id', Auth::user()->tenant_id)->with('patientUser')
                ->whereHas('patientUser', function (Builder $query) {
                    $query->where('status', 1);
                })->get()->pluck('patientUser.full_name', 'id')->sort();
        }

        return $patients;
    }

    /**
     * @return mixed
     */
    public function getPatientAssociatedData(int $patientId)
    {
        $patientData = Patient::with([
            'bills',
            'invoices',
            'appointments.doctor.doctorUser',
            'appointments.doctor.department',
            'admissions.doctor.doctorUser',
            'cases.doctor.doctorUser',
            'advancedpayments',
            'documents.media',
            'documents.documentType',
            'patientUser',
            'vaccinations.vaccination',
            'address',
        ])->findOrFail($patientId);

        return $patientData;
    }

    public function createNotification(array $input)
    {
        try {
            $receptionists = Receptionist::pluck('user_id', 'id')->toArray();

            $userIds = [];
            foreach ($receptionists as $key => $userId) {
                $userIds[$userId] = Notification::NOTIFICATION_FOR[Notification::RECEPTIONIST];
            }
            $users = getAllNotificationUser($userIds);

            foreach ($users as $key => $notification) {
                if (isset($key)) {
                    addNotification([
                        Notification::NOTIFICATION_TYPE['Patient'],
                        $key,
                        $notification,
                        $input['first_name'] . ' ' . $input['last_name'] . ' added as a patient.',
                    ]);
                }
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
