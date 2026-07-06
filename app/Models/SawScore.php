<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SawScore extends Model
{
    protected $guarded = [];

    protected $casts = [
        'assessed_at' => 'date',
    ];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'assessment_period_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function criterion()
    {
        return $this->belongsTo(SawCriterion::class, 'saw_criterion_id');
    }
}
