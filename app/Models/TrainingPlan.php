<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingPlan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'planned_at' => 'date',
    ];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'assessment_period_id');
    }

    public function participants()
    {
        return $this->hasMany(TrainingPlanParticipant::class);
    }
}
