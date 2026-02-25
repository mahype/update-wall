<?php

namespace App\Models;

use App\Enums\MachineStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostname',
        'display_name',
        'api_token_id',
        'last_report_at',
        'total_updates',
        'has_security',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'last_report_at' => 'datetime',
            'has_security' => 'boolean',
            'status' => MachineStatus::class,
        ];
    }

    public function apiToken(): BelongsTo
    {
        return $this->belongsTo(ApiToken::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class)->orderByDesc('reported_at');
    }

    public function latestReport(): HasOne
    {
        return $this->hasOne(Report::class)->latestOfMany('reported_at');
    }

    public function isStale(int $thresholdHours = 25): bool
    {
        return $this->last_report_at
            && $this->last_report_at->lt(now()->subHours($thresholdHours));
    }
}
