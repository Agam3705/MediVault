<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function userSettings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function sessionsTracker()
    {
        return $this->hasMany(SessionsTracker::class);
    }

    /**
     * Get a setting value, returning a default if not set.
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->userSettings()->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Save/update a setting value.
     */
    public function setSetting(string $key, $value)
    {
        $this->userSettings()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
