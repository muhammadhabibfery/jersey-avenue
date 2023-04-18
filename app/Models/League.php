<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    use HasFactory;

    /**
     * Get the jerseys for the league.
     */
    public function jerseys(): HasMany
    {
        return $this->hasMany(Jersey::class);
    }

    /**
     * Set the league's name.
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = ucwords($value);
    }

    /**
     * Set the league's country.
     */
    public function setCountryAttribute(string $value): void
    {
        $this->attributes['country'] = ucwords($value);
    }

    /**
     * get the league's image with custom directory path.
     */
    public function getImage(): string
    {
        return file_exists(public_path("assets/images/leagues/{$this->image}"))
            ? asset("assets/images/leagues/{$this->image}")
            : asset("storage/leagues/{$this->image}");
    }
}
