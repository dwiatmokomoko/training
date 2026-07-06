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
        'work_unit_id',
        'education_level',
        'work_experience',
        'current_position_start_date',
        'last_promotion_date',
        'last_training_date',
        'birth_date',
        'gender',
        'address',
        'phone'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'current_position_start_date' => 'date',
        'last_promotion_date' => 'date',
        'last_training_date' => 'date',
        'work_experience' => 'integer'
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
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
        return $this->birth_date?->age;
    }

    public function getCurrentPositionYearsAttribute(): int
    {
        return $this->current_position_start_date
            ? $this->current_position_start_date->diffInYears(now())
            : (int) $this->work_experience;
    }

    public function getYearsSinceLastTrainingAttribute(): ?int
    {
        return $this->last_training_date?->diffInYears(now());
    }

    public function getYearsSinceLastPromotionAttribute(): ?int
    {
        return $this->last_promotion_date?->diffInYears(now());
    }
}
