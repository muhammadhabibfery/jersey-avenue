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

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'invoice_number';
    }
}
