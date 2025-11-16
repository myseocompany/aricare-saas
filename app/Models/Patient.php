<?php

namespace App\Models;

use App\Traits\PopulateTenantID;
use Illuminate\Support\Str;
use App\Enums\Ethnicity;
use App\Enums\EducationLevel;
use App\Models\Rda\RdaOccupation;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;
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

    protected $fillable = [
        'user_id',
        'tenant_id',
        'template_id',
        'patient_unique_id',
        'custom_field',
        'record_number',
        'affiliate_number',
        'document_type',
        'type_id',
        'sex_code',
        'rips_country_id',
        'rips_department_id',
        'rips_municipality_id',
        'zone_code',
        'country_of_origin_id',
        'rips_identification_type_id',
        'document_number',
        'contact_email',
        'marital_status_id',
        'birth_place',
        'residence_address',
        'occupation',
        'rda_occupation_id',
        'ethnicity_id',
        'education_level_id',
        'phone_secondary',
        'responsible_name',
        'responsible_phone',
        'responsible_relationship',
        'emergency_contact_name',
        'emergency_contact_phone',
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
        'marital_status_id' => 'integer',
        'ethnicity_id' => 'integer',
        'education_level_id' => 'integer',
        'rda_occupation_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::$rules = static::validationRules();
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static array $rules = [];

    public static function validationRules(): array
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'contact_email' => 'nullable|email:filter',
            'password' => 'nullable|same:password_confirmation|min:6',
            'gender' => 'required',
            'dob' => 'nullable|date',
            'phone' => 'nullable|numeric',
            'image' => 'mimes:jpeg,png,jpg,gif,webp',
            'marital_status_id' => 'nullable|integer',
            'birth_place' => 'nullable|string|max:255',
            'residence_address' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'rda_occupation_id' => ['nullable', 'integer', 'exists:rda_occupations,id'],
            'ethnicity_id' => ['nullable', 'integer', Rule::in(Ethnicity::values())],
            'education_level_id' => ['nullable', 'integer', Rule::in(EducationLevel::values())],
            'phone_secondary' => 'nullable|string|max:50',
            'responsible_name' => 'nullable|string|max:255',
            'responsible_phone' => 'nullable|string|max:50',
            'responsible_relationship' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
        ];
    }

    public static function getActivePatientNamesOld()
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


    public static function getActivePatientNames()
{
return self::with('user')
    ->whereHas('user', fn ($q) => $q->where('status', User::ACTIVE))
    ->where('tenant_id', getLoggedInUser()->tenant_id)
    ->get()
    ->mapWithKeys(function ($patient) {
        return [$patient->id => $patient->user->full_name];
    });

}



    public function patientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getDocumentTypeAttribute()
    {
        return $this->user ? $this->user->rips_identification_type_id : null;
    }

    public function rdaOccupation(): BelongsTo
    {
        return $this->belongsTo(RdaOccupation::class, 'rda_occupation_id');
    }

    /*public function getDocumentTypeNameAttribute()
    {
        return $this->user && $this->user->documentType
            ? $this->user->documentType->name
            : null;
    }*/

    public function getDocumentNumberAttribute()
    {
        return $this->user ? $this->user->rips_identification_number : null;
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'owner');
    }

    public function getEthnicityLabelAttribute(): ?string
    {
        return Ethnicity::tryFrom((int) $this->ethnicity_id)?->label();
    }

    public function getEducationLevelLabelAttribute(): ?string
    {
        return EducationLevel::tryFrom((int) $this->education_level_id)?->label();
    }

    public function cases(): HasMany
    {
        return $this->hasMany(PatientCase::class, 'patient_id');
    }

    public function getEmailForDisplayAttribute(): ?string
    {
        return $this->contact_email ?? $this->patientUser->email ?? null;
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
            'email_id' => $this->contact_email ?? $this->patientUser->email ?? __('messages.common.n/a'),
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

   /* public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type', 'abbreviation');
    }*/


    public function patientType()
    {
        return $this->belongsTo(PatientType::class);
    }

    

    // Nuevvas relaciones agregadas por Julian
    
    // Relación con país de origen (usando country_of_origin como código)
    public function originCountry()
    {
        return $this->belongsTo(\App\Models\Rips\RipsCountry::class, 'country_of_origin_id', 'id');
    }

    // Relación con país de residencia (usando country_code)
    public function residenceCountry()
    {
        return $this->belongsTo(\App\Models\Rips\RipsCountry::class, 'rips_country_id', 'id');
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
        return $this->belongsTo(\App\Models\Rips\RipsIdentificationType::class);
    }

    /*public function ripsUserType()
    {
        return $this->belongsTo(\App\Models\Rips\RipsUserType::class);
    }*/
    public function ripsUserType()
    {
        return $this->belongsTo(\App\Models\Rips\RipsUserType::class, 'type_id');
    }

    public function ripsDepartment()
    {
        return $this->belongsTo(\App\Models\Rips\Department::class);
    }
    
    public function ripsMunicipality()
    {
        return $this->belongsTo(\App\Models\Rips\RipsMunicipality::class);
    }

    public function ripsCountry()
    {
        return $this->belongsTo(\App\Models\Rips\RipsCountry::class, 'rips_country_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->user
            ? "{$this->user->first_name} {$this->user->last_name}"
            : '';
    }

    public function userIdentificationType()
    {
        return $this->hasOneThrough(
            \App\Models\Rips\RipsIdentificationType::class,
            User::class,
            'id', // Local key on users
            'id', // Local key on rips_identification_types
            'user_id', // Foreign key on patients
            'rips_identification_type_id' // Foreign key on users
        );
    }


}
