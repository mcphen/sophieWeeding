<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProspectToken extends Model
{
    protected $fillable = ['email', 'token', 'expires_at', 'used_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public static function createForEmail(string $email): self
    {
        // Invalider les anciens tokens non utilisés pour cet email
        self::where('email', $email)->whereNull('used_at')->delete();

        return self::create([
            'email'      => $email,
            'token'      => Str::random(64),
            'expires_at' => now()->addHour(),
        ]);
    }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }

    public function markUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
