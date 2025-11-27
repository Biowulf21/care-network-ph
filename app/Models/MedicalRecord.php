<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'clinic_id', 'user_id', 'consultation_date', 'vitals', 'doctor_notes', 'diagnosis', 'treatment_plan', 'medical_history', 'philhealth', 'documents_checklist', 'admission', 'discharge'
    ];

    protected $casts = [
        'vitals' => 'array',
        'medical_history' => 'array',
        'philhealth' => 'array',
        'documents_checklist' => 'array',
        'admission' => 'array',
        'discharge' => 'array',
        'consultation_date' => 'date',
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
}
