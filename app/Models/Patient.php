<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'photo',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'sex',
        'gender',
        'philhealth_number',
        'philhealth_id',
        'patient_id',
        'address',
        'city',
        'province',
        'region',
        'zip_code',
        'phone',
        'email',
        'civil_status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'insurance_info',
        'height',
        'weight',
        'blood_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'insurance_info' => 'array',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            if (empty($patient->patient_id)) {
                do {
                    $candidate = 'P' . now()->format('Ymd') . str_pad((string) mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
                } while (self::where('patient_id', $candidate)->exists());

                $patient->patient_id = $candidate;
            }
        });
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null;
    }

    public function getBmiAttribute()
    {
        if ($this->height && $this->weight) {
            $heightInMeters = $this->height / 100;

            return round($this->weight / ($heightInMeters * $heightInMeters), 1);
        }

        return null;
    }

    public function getLatestVitalsAttribute()
    {
        return $this->medicalRecords()->whereNotNull('vitals')->latest()->first()?->vitals;
    }
}
