<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'name',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'phone',
        'email',
        'status',
    ];

    /**
     * Get the gym that owns this location
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Get all users assigned to this location
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'location_user')
            ->withTimestamps();
    }

    /**
     * Get all groups offered at this location
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_location')
            ->withTimestamps();
    }
}
