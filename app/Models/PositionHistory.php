<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionHistory extends Model
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

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
