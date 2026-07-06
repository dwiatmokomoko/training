<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceScore extends Model
{
    protected $guarded = [];

    protected $casts = [
        'assessed_at' => 'date',
    ];

    public function indicator()
    {
        return $this->belongsTo(PerformanceIndicator::class, 'performance_indicator_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
