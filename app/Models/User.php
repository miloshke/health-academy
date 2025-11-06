<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    // Role Constants
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_GYM_ADMIN = 'gym_admin';
    const ROLE_TRAINER = 'trainer';
    const ROLE_TRAINEE = 'trainee';

    const ROLE_NAMES = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_GYM_ADMIN => 'Gym Admin',
        self::ROLE_TRAINER => 'Trainer',
        self::ROLE_TRAINEE => 'Trainee',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'phone',
        'status',
        'birthdate',
        'gender',
        'password',
        'role',
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
            'birthdate' => 'date',
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the gym that owns this user
     */
    public function gym(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Get the primary location for this user
     */
    public function primaryLocation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class, 'primary_location_id');
    }

    /**
     * Get all locations this user is assigned to
     */
    public function locations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_user')
            ->withTimestamps();
    }

    /**
     * Get all groups this user is enrolled in
     */
    public function groups(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->withPivot('status', 'enrolled_at')
            ->withTimestamps();
    }

    /**
     * Get all packages this user has purchased
     */
    public function packages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_user')
            ->withPivot([
                'price_paid',
                'purchased_at',
                'starts_at',
                'expires_at',
                'status',
                'payment_status',
                'payment_method',
                'transaction_id',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * Get active packages only
     */
    public function activePackages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->packages()
            ->wherePivot('status', 'active')
            ->wherePivot('expires_at', '>', now());
    }

    /**
     * Check if user has an active package
     */
    public function hasActivePackage(): bool
    {
        return $this->activePackages()->exists();
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if user is a gym admin
     */
    public function isGymAdmin(): bool
    {
        return $this->role === self::ROLE_GYM_ADMIN;
    }

    /**
     * Check if user is a trainer
     */
    public function isTrainer(): bool
    {
        return $this->role === self::ROLE_TRAINER;
    }

    /**
     * Check if user is a trainee
     */
    public function isTrainee(): bool
    {
        return $this->role === self::ROLE_TRAINEE;
    }
}
