<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criteria extends Model
{
    protected $table = 'criteria'; // Specify table name explicitly
    
    protected $fillable = [
        'name',
        'description',
        'weight',
        'type'
    ];

    protected $casts = [
        'weight' => 'decimal:2'
    ];

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
