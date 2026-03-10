<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'nip',
        'name',
        'email',
        'position_id',
        'education_level',
        'work_experience',
        'birth_date',
        'gender',
        'address',
        'phone'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'work_experience' => 'integer'
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function trainingNeeds(): HasMany
    {
        return $this->hasMany(TrainingNeed::class);
    }

    public function getAgeAttribute()
    {
        return $this->birth_date->age;
    }
}
