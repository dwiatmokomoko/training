<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingHistory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
