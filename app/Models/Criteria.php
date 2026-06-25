<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criteria extends Model
{
    public const LATEST_TNA_CODES = ['C1', 'C2', 'C3', 'C4', 'C5'];

    protected $table = 'criteria'; // Specify table name explicitly
    
    protected $fillable = [
        'code',
        'name',
        'description',
        'weight',
        'type',
        'importance_rating'
    ];

    protected $casts = [
        'weight' => 'decimal:3',
        'importance_rating' => 'integer'
    ];

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function scopeLatestTna($query)
    {
        return $query->whereIn('code', self::LATEST_TNA_CODES)->orderBy('code');
    }
}
