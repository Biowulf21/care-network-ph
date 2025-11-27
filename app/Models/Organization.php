<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'address', 'phone'];

    public function clinics()
    {
        return $this->hasMany(Clinic::class);
    }
}
