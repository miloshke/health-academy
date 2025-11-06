<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'max_participants',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    /**
     * Get the gym that owns this group
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Get all locations where this group is offered
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'group_location')
            ->withTimestamps();
    }

    /**
     * Get all users enrolled in this group
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->withPivot('status', 'enrolled_at')
            ->withTimestamps();
    }

    /**
     * Get the count of enrolled users
     */
    public function enrolledCount(): int
    {
        return $this->users()->wherePivot('status', 'enrolled')->count();
    }

    /**
     * Check if group is full
     */
    public function isFull(): bool
    {
        if (!$this->max_participants) {
            return false;
        }

        return $this->enrolledCount() >= $this->max_participants;
    }
}
