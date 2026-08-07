<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingNeed extends Model
{
    protected $fillable = [
        'employee_id',
        'training_type',
        'training_description',
        'saw_score',
        'priority_rank',
        'eligibility_status',
        'status',
        'recommended_date',
        'period_year',
        'period_semester',
        'period_label',
        'notes'
    ];

    protected $casts = [
        'saw_score' => 'decimal:4',
        'priority_rank' => 'integer',
        'period_year' => 'integer',
        'period_semester' => 'integer',
        'recommended_date' => 'date'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getEligibilityLabelAttribute(): string
    {
        return ($this->eligibility_status ?: ((float) $this->saw_score > 0.9 ? 'layak' : 'cadangan')) === 'layak'
            ? 'Layak'
            : 'Cadangan';
    }
}
