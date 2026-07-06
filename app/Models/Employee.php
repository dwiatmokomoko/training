<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    protected $casts = [
        'birth_date' => 'date',
        'position_started_at' => 'date',
    ];

    public function group()
    {
        return $this->belongsTo(EmployeeGroup::class, 'employee_group_id');
    }

    public function unit()
    {
        return $this->belongsTo(WorkUnit::class, 'work_unit_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function trainingHistories()
    {
        return $this->hasMany(TrainingHistory::class);
    }

    public function positionHistories()
    {
        return $this->hasMany(PositionHistory::class);
    }

    public function performanceScores()
    {
        return $this->hasMany(PerformanceScore::class);
    }

    public function sawScores()
    {
        return $this->hasMany(SawScore::class);
    }
}
