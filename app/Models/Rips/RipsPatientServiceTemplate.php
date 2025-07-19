<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RipsPatientServiceTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'tenant_id',
        'user_id',
        'is_public',
    ];

    public function consultations()
    {
        return $this->hasMany(RipsPatientServiceTemplateConsultation::class, 'template_id');
    }

    public function diagnoses()
    {
        return $this->hasMany(RipsPatientServiceTemplateConsultationDiagnosis::class, 'template_id');
    }

    public function procedures()
    {
        return $this->hasMany(RipsPatientServiceTemplateProcedure::class, 'template_id');
    }
}
