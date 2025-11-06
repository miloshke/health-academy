<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'name',
        'description',
        'price',
        'duration_days',
        'benefits',
        'group_access_limit',
        'unlimited_access',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'benefits' => 'array',
            'unlimited_access' => 'boolean',
        ];
    }

    /**
     * Get the gym that owns this package
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Get all users who have purchased this package
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'package_user')
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
     * Get active user subscriptions
     */
    public function activeSubscriptions(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('status', 'active')
            ->wherePivot('expires_at', '>', now());
    }
}
