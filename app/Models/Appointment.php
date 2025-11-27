<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'clinic_id',
        'user_id',
        'appointment_date',
        'appointment_time',
        'appointment_type',
        'status',
        'notes',
        'service_type',
        'specialty',
        'duration',
        'is_urgent',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime',
        'is_urgent' => 'boolean',
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

    public function getFormattedDateTimeAttribute()
    {
        return $this->appointment_date->format('M d, Y').' at '.$this->appointment_time->format('g:i A');
    }
}
