<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequestLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ip',
        'status',
        'token_id',
        'hostname',
        'detail',
        'payload',
        'response',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'payload'    => 'array',
            'response'   => 'array',
        ];
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(ApiToken::class, 'token_id');
    }
}
