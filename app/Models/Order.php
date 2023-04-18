<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes of status.
     */
    public static $status = ['IN CART', 'PENDING', 'SUCCESS', 'FAILED'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'courier_services' => 'array',
    ];

    /**
     * Get the user that owns the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The jerseys that belong to the order.
     */
    public function jerseys(): BelongsToMany
    {
        return $this->belongsToMany(Jersey::class)
            ->withPivot(['id', 'size', 'quantity', 'total_price', 'nameset'])
            ->withTimestamps();
    }

    public function getStatusBadgeAttribute(): string
    {
        switch ($this->status) {
            case 'PENDING':
                $badge = "px-2 py-1 text-warning-700 bg-warning-200 rounded-lg";
                break;
            case 'SUCCESS':
                $badge = "px-2 py-1 text-success-700 bg-success-200 rounded-lg";
                break;
            default:
                $badge = "px-2 py-1 text-danger-700 bg-danger-200 rounded-lg";
                break;
        }

        return $badge;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'invoice_number';
    }
}
