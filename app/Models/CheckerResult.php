<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CheckerResult extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'name',
        'summary',
        'error',
        'update_count',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function packageUpdates(): HasMany
    {
        return $this->hasMany(PackageUpdate::class);
    }

    public function hasError(): bool
    {
        return ! is_null($this->error);
    }
}
