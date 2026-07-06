<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobFamily extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
}
