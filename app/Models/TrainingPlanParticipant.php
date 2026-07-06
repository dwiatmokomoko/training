<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingPlanParticipant extends Model
{
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
