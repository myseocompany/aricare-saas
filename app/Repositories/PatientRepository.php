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

    // SQLSTATE[HY000]: General error: 1364 Field 'password' doesn't have a default value (Connection: mysql, SQL: insert into `users` (`first_name`, `last_name`, `email`, `dob`, `phone`, `gender`, `status`, `blood_group`, `city`, `facebook_url`, `twitter_url`, `instagram_url`, `linkedIn_url`, `department_id`, `language`, `updated_at`, `created_at`) values (Illiana, Stanley, pynecajez@mailinator.com, 2009-06-14, 079944 37176, 1, 1, ?, Ipsum ab non nihil , https://www.kowu.info, https://www.ceqovu.co.uk, https://www.fetexykym.com, https://www.wen.net, 3, ar, 2024-10-30 05:24:47, 2024-10-30 05:24:47))



    public function store(array $input, bool $mail = true)
    {
        try {
            // $input['phone'] = preparePhoneNumber($input, 'phone');
            //$input['department_id'] = Department::whereName('Patient')->first()->id;
            $input['department_id'] = Department::where('name', 'Patient')->value('id') ?? 3;

            $input['password'] = Hash::make($input['password']);
            if (!empty(getSuperAdminSettingValue()['default_language']->value)) {
                $input['language'] = getSuperAdminSettingValue()['default_language']->value;
            }
            $input['tenant_id'] = $input['tenant_id'] ?? getLoggedInUser()?->tenant_id;


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
                'document_type' => $input['document_type'] ?? null,
            
                // Ya existentes:
                'rips_identification_type_id' => $input['rips_identification_type_id'] ?? null,
                'document_number' => $input['document_number'] ?? null,
                'type_id' => $input['patient_type_id'] ?? null,
                'birth_date' => $input['dob'] ?? null,
                'sex_code' => Gender::from((int) $input['gender'])->sexCode(),
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

    public function update($patient, $input)
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
