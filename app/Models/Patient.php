<?php

namespace App\Models;

use Str;
use Illuminate\Support\Carbon;
use App\Traits\PopulateTenantID;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class Patient
 *
 * @version February 14, 2020, 5:53 am UTC
 *
 * @property int user_id
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder|Patient newModelQuery()
 * @method static Builder|Patient newQuery()
 * @method static Builder|Patient query()
 * @method static Builder|Patient whereCreatedAt($value)
 * @method static Builder|Patient whereId($value)
 * @method static Builder|Patient whereUpdatedAt($value)
 * @method static Builder|Patient whereUserId($value)
 *
 * @mixin Model
 *
 * @property int $is_default
 * @property-read \App\Models\Address|null $address
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PatientAdmission[] $admissions
 * @property-read int|null $admissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AdvancedPayment[] $advancedpayments
 * @property-read int|null $advancedpayments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Appointment[] $appointments
 * @property-read int|null $appointments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bill[] $bills
 * @property-read int|null $bills_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PatientCase[] $cases
 * @property-read int|null $cases_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Document[] $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\VaccinatedPatients[] $vaccinations
 * @property-read int|null $vaccinations_count
 *
 * @method static Builder|Patient whereIsDefault($value)
 */
class Patient extends Model implements HasMedia
{
    use BelongsToTenant, PopulateTenantID , InteractsWithMedia;

    public $table = 'patients';

    public $fillable = [
        'user_id',
        'tenant_id',
        'template_id',
        'patient_unique_id',
        'custom_field',
        'rips_identification_type_id',
        'rips_user_type_id',
    ];

    const STATUS_ALL = 2;

    const ACTIVE = 1;

    const INACTIVE = 0;

    const STATUS_ARR = [
        self::STATUS_ALL => 'All',
        self::ACTIVE => 'Active',
        self::INACTIVE => 'Deactive',
    ];

    const FILTER_STATUS_ARR = [
        0 => 'All',
        1 => 'Active',
        2 => 'Deactive',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'custom_field' => 'array',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email:filter|unique:users,email',
        'password' => 'nullable|same:password_confirmation|min:6',
        'gender' => 'required',
        'dob' => 'nullable|date',
        'phone' => 'nullable|numeric',
        'image' => 'mimes:jpeg,png,jpg,gif,webp',
    ];

    public static function getActivePatientNames()
    {
        $patients = DB::table('users')
            ->where('status', User::ACTIVE)
            ->where('patients.tenant_id', getLoggedInUser()->tenant_id)
            ->join('patients', 'users.id', '=', 'patients.user_id')
            ->select(['users.first_name', 'users.last_name', 'patients.id'])
            ->orderBy('first_name')
            ->get();
        $patientsNames = collect();
        foreach ($patients as $patient) {
            $patientsNames[$patient->id] = ucfirst($patient->first_name).' '.ucfirst($patient->last_name);
        }

        return $patientsNames;
    }

    public function patientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'owner');
    }

    public function cases(): HasMany
    {
        return $this->hasMany(PatientCase::class, 'patient_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(PatientAdmission::class, 'patient_id');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class, 'patient_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'patient_id');
    }

    public function advancedpayments(): HasMany
    {
        return $this->hasMany(AdvancedPayment::class, 'patient_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'patient_id');
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(VaccinatedPatients::class, 'patient_id');
    }

    public function opd()
    {
        return $this->hasMany(OpdPatientDepartment::class, 'patient_id');
    }

    public function SmartCardTemplate(): BelongsTo
    {
        return $this->belongsTo(SmartPatientCard::class, 'template_id');
    }

    public function prepareData()
    {
        return [
            'id' => $this->id ?? __('messages.common.n/a'),
            'patient_name' => $this->patientUser->full_name ?? __('messages.common.n/a'),
            'phone_no' => $this->patientUser->phone ?? __('messages.common.n/a'),
            'patient_image' => $this->patientUser->getApiImageUrlAttribute() ?? __('messages.common.n/a'),
        ];
    }

    public function preparePatientDetail(){
        return [
            'id' => $this->id ?? __('messages.common.n/a'),
            'patient_name' => $this->patientUser->full_name ?? __('messages.common.n/a'),
            'email_id' => $this->patientUser->email ?? __('messages.common.n/a'),
            'phone_no' => $this->patientUser->phone ?? __('messages.common.n/a'),
            'blood_group' => $this->patientUser->blood_group ?? __('messages.common.n/a'),
        ];
    }

    public static function generateUniquePatientId()
    {
        do {
            $patientUniqueId = Str::random(8);
            $isExist = self::where('patient_unique_id', $patientUniqueId)->exists();
        } while ($isExist);

        return $patientUniqueId;
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type', 'abbreviation');
    }


    public function patientType()
    {
        return $this->belongsTo(PatientType::class);
    }

    // Nuevvas relaciones agregadas por Julian
    
    // Relación con país de origen (usando country_of_origin como código)
    public function originCountry()
    {
        return $this->belongsTo(RipsCountry::class, 'country_of_origin', 'code');
    }

    // Relación con país de residencia (usando country_code)
    public function residenceCountry()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
    // Convertir zone_code a string (accesor)
    public function getZoneTextAttribute()
    {
        return match((string)$this->zone_code) {
            '1' => 'Urbana',
            '2' => 'Rural',
            default => 'No especificada'
        };
    }

    public function ripsIdentificationType()
    {
        return $this->belongsTo(\App\Models\RipsIdentificationType::class);
    }

    public function ripsUserType()
    {
        return $this->belongsTo(\App\Models\RipsUserType::class);
    }
    public function ripsDepartment()
    {
        return $this->belongsTo(RipsDepartment::class);
    }
    
    public function ripsMunicipality()
    {
        return $this->belongsTo(RipsMunicipality::class);
    }
    
}
