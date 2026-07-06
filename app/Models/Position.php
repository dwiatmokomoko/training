<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = [
        'job_family_id',
        'name',
        'description',
        'level'
    ];

    protected $casts = [
        'level' => 'string'
    ];

    public function jobFamily(): BelongsTo
    {
        return $this->belongsTo(JobFamily::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
