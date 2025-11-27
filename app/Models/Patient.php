<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'sex',
        'philhealth_number',
        'address',
        'phone',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $dates = ['date_of_birth'];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
