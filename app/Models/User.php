<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organization_id',
        'clinic_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function belongsToOrganization(int $orgId): bool
    {
        return $this->organization_id === $orgId;
    }

    public function assignRole(string $roleName): void
    {
        $role = Role::firstWhere('name', $roleName);

        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
