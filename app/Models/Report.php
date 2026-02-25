<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'machine_id',
        'reported_at',
        'total_updates',
        'has_security',
        'raw_payload',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime',
            'has_security' => 'boolean',
            'raw_payload' => 'json',
            'created_at' => 'datetime',
        ];
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function checkerResults(): HasMany
    {
        return $this->hasMany(CheckerResult::class);
    }
}
