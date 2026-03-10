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
        'status',
        'recommended_date',
        'notes'
    ];

    protected $casts = [
        'saw_score' => 'decimal:4',
        'priority_rank' => 'integer',
        'recommended_date' => 'date'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
