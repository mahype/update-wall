<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'token_hash',
        'expires_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function machines(): HasMany
    {
        return $this->hasMany(Machine::class);
    }

    public function isValid(): bool
    {
        return is_null($this->revoked_at)
            && (is_null($this->expires_at) || $this->expires_at->isFuture());
    }

    public static function createFor(User $user, string $name, ?Carbon $expiresAt = null): array
    {
        $plainText = Str::random(64);
        $token = self::create([
            'user_id' => $user->id,
            'name' => $name,
            'token_hash' => hash('sha256', $plainText),
            'expires_at' => $expiresAt,
        ]);

        return ['token' => $token, 'plain_text' => $plainText];
    }

    public function revoke(): void
    {
        $this->update(['revoked_at' => now()]);
    }
}
