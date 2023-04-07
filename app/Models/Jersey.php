<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Jersey extends Model
{
    use HasFactory;

    /**
     * The attributes of sizes.
     */
    public static $sizes = ['S', 'M', 'L', 'XL'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stock' => 'array',
    ];

    /**
     * Get the league that owns the jersey
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * The orders that belong to the jersey.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->withPivot(['quantity', 'total_price', 'nameset'])
            ->withTimestamps();
    }

    /**
     * get the jerseys's image with custom directory path.
     */
    public function getImage(): string
    {
        return file_exists(public_path("assets/images/jerseys/{$this->image}"))
            ? asset("assets/images/jerseys/{$this->image}")
            : asset("storage/jerseys/{$this->image}");
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope a query to only include jersey where has league.
     */
    public function scopeHasLeague(Builder $query, ?string $slug = null): void
    {
        $query->whereHas('league', fn (Builder $query): Builder => $query->where('slug', $slug));
    }
}
