<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkUnit extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(WorkUnit::class, 'parent_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
