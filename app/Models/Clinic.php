<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'name', 'code', 'address', 'phone'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
