<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jersey extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes of sizes.
     */
    public static $sizes = ['S', 'M', 'L', 'XL'];

    /**
     * The attributes of sizes.
     */
    public static $topCount = 4;

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
            ->withPivot(['id', 'size', 'quantity', 'total_price', 'nameset'])
            ->withTimestamps();
    }

    /**
     * Set the jersey's name.
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = ucwords($value);
    }

    /**
     * Set the jersey's type.
     */
    public function setTypeAttribute(string $value): void
    {
        $this->attributes['type'] = ucwords($value);
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

    /**
     * Scope a query best seller jersey.
     */
    public function scopeBestSeller(Builder $query): void
    {
        $query->where('sold', '>=', self::$topCount);
    }
}
