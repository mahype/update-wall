<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageUpdate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'checker_result_id',
        'name',
        'current_version',
        'new_version',
        'type',
        'priority',
        'source',
        'phasing',
    ];

    public function checkerResult(): BelongsTo
    {
        return $this->belongsTo(CheckerResult::class);
    }

    public function isSecurity(): bool
    {
        return $this->type === 'security';
    }

    public function isCritical(): bool
    {
        return $this->priority === 'critical';
    }
}
