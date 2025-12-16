<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'clinic_id', 'user_id', 'doctor_id', 'consultation_date', 'vitals', 'doctor_notes', 'diagnosis', 'treatment_plan', 'medical_history', 'philhealth', 'documents_checklist', 'admission', 'discharge',
        // Enhanced clinical fields
        'emar_data', 'chief_complaint', 'history_present_illness', 'physical_examination', 'assessment_plan', 'prescriptions', 'disposition', 'encounter_type', 'consultation_type', 'allergies', 'family_history', 'immunization_history', 'social_history', 'next_appointment', 'provider_notes', 'diagnosis_codes',
    ];

    protected $casts = [
        'vitals' => 'array',
        'medical_history' => 'array',
        'philhealth' => 'array',
        'documents_checklist' => 'array',
        'admission' => 'array',
        'discharge' => 'array',
        'consultation_date' => 'date',
        'next_appointment' => 'date',
        'emar_data' => 'array',
        'prescriptions' => 'array',
        'allergies' => 'array',
        'family_history' => 'array',
        'immunization_history' => 'array',
        'social_history' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
