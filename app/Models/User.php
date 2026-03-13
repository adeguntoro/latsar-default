<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'session_token',
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
        ];
    }

    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class);
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function isPasswordUsedBefore($password, $limit = 5): bool
    {
        $recentPasswords = $this->passwordHistories()
            ->latest('changed_at')
            ->limit($limit)
            ->pluck('password_hash')
            ->toArray();

        foreach ($recentPasswords as $hash) {
            if (\Hash::check($password, $hash)) {
                return true;
            }
        }

        return false;
    }

    public function recordPasswordHistory($password): void
    {
        $this->passwordHistories()->create([
            'password_hash' => \Hash::make($password),
            'changed_at' => now(),
        ]);
    }

    /**
     * Get the request files created by this user.
     */
    public function requestFiles()
    {
        return $this->hasMany(RequestFile::class, 'user_served');
    }
}