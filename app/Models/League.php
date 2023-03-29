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
}
