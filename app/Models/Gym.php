<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gym extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'email',
        'phone',
        'website',
        'status',
    ];

    /**
     * Get all locations for this gym
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Get all users belonging to this gym
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all groups for this gym
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get all packages for this gym
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
