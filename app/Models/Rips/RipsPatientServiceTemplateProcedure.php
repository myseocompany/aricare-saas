<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RipsPatientServiceTemplateProcedure extends Model
{
    use HasFactory;

    protected $fillable = [
        'rips_patient_service_template_id',
        'rips_admission_route_id',
        'rips_service_group_mode_id',
        'rips_service_group_id',
        'rips_collection_concept_id',
        'rips_technology_purpose_id',
        'mipres_id',
        'authorization_number',
        'rips_cups_id',
        'cie10_id',
        'surgery_cie10_id',
        'rips_complication_cie10_id',
        'service_value',
        'copayment_value',
        'copayment_receipt_number',
    ];

    public function template()
    {
        return $this->belongsTo(RipsPatientServiceTemplate::class, 'rips_patient_service_template_id');
    }
}
