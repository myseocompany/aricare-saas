<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Address;
use App\Models\Patient;
use App\Models\Department;
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

    public function store(array $input, bool $mail = true)
{
    try {
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
            $input['email'] = 'paciente_' . uniqid() . '@fakeemail.local';
        }

        // Idioma por defecto
        if (!empty(getSuperAdminSettingValue()['default_language']->value)) {
            $input['language'] = getSuperAdminSettingValue()['default_language']->value;
        }

        $tenantId = $input['tenant_id'] ?? getLoggedInUser()?->tenant_id;

        // ==========================
        // 1️⃣ Datos para USERS
        // ==========================
        $userData = [
            'first_name'                  => $input['first_name'] ?? '',
            'last_name'                   => $input['last_name'] ?? '',
            'email'                       => $input['email'],
            'password'                    => $input['password'],
            'department_id'               => $departmentId,
            'gender'                      => isset($input['gender']) ? (int) $input['gender'] : null,
            'tenant_id'                   => $tenantId,
            'status'                      => $input['status'] ?? 1,
            'language'                    => $input['language'] ?? 'es',
            'rips_identification_type_id' => $input['rips_identification_type_id'] ?? null,
            'rips_identification_number'  => $input['rips_identification_number'] ?? null,
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
            'birth_date'           => !empty($input['birth_date'])
            ? \Carbon\Carbon::parse($input['birth_date'])->format('Y-m-d')
            : null,
            'rips_country_id'      => $input['rips_country_id'] ?? null,
            'rips_department_id'   => $input['rips_department_id'] ?? null,
            'rips_municipality_id' => $input['rips_municipality_id'] ?? null,
            'zone_code'            => $input['zone_code'] ?? null,
            'country_of_origin_id' => $input['country_of_origin_id'] ?? null,
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

        return $user;

    } catch (Exception $e) {
        \Log::error('Excepción al crear paciente', ['exception' => $e]);
        throw new \RuntimeException('Error creando el paciente: ' . $e->getMessage(), previous: $e);
    }
}


    public function store2(array $input, bool $mail = true)
    {
        try {
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
                // Generar un email falso único (puedes usar uuid o número aleatorio)
                $input['email'] = 'paciente_' . uniqid() . '@fakeemail.local';
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
                'birth_date'           => !empty($input['birth_date'])
                ? \Carbon\Carbon::parse($input['birth_date'])->format('Y-m-d')
                : null,
                //'sex_code' => Gender::from((int) $input['gender'])->sexCode(),
                //'gender' => $input['gender'] ?? null,
                //'rips_identification_type_id' => $input['rips_identification_type_id'] ?? null,
                
                'rips_country_id' => $input['rips_country_id'] ?? null,
                'rips_department_id' => $input['rips_department_id'] ?? null,
                'rips_municipality_id' => $input['rips_municipality_id'] ?? null,
                'zone_code' => $input['zone_code'] ?? null,
                'country_of_origin_id' => $input['country_of_origin_id'] ?? null,
                
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
            $input['dob'] = (!empty($input['dob'])) ? $input['dob'] : null;
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

        // ==========================
        // 2️⃣ Datos para USERS
        // ==========================
        $userData = [
            'first_name'                  => $input['first_name'] ?? $user->first_name,
            'last_name'                   => $input['last_name'] ?? $user->last_name,
            'email'                       => $input['email'] ?? $user->email,
            'gender'                      => isset($input['gender']) ? (int) $input['gender'] : $user->gender,
            'tenant_id'                   => $input['tenant_id'] ?? $user->tenant_id,
            'status'                      => $input['status'] ?? $user->status,
            'language'                    => $input['language'] ?? $user->language,
            'rips_identification_type_id' => $input['rips_identification_type_id'] ?? $user->rips_identification_type_id,
            'rips_identification_number'  => $input['rips_identification_number'] ?? $user->rips_identification_number,
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
            'birth_date'           => array_key_exists('birth_date', $input) && !empty($input['birth_date'])
            ? \Carbon\Carbon::parse($input['birth_date'])->format('Y-m-d')
            : $patient->birth_date,
            'rips_country_id'      => $input['rips_country_id'] ?? $patient->rips_country_id,
            'rips_department_id'   => $input['rips_department_id'] ?? $patient->rips_department_id,
            'rips_municipality_id' => $input['rips_municipality_id'] ?? $patient->rips_municipality_id,
            'zone_code'            => $input['zone_code'] ?? $patient->zone_code,
            'country_of_origin_id' => $input['country_of_origin_id'] ?? $patient->country_of_origin_id,
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
