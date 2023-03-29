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
            ->withPivot(['quantity', 'total_price', 'nameset'])
            ->withTimestamps();
    }
}
