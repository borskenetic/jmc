<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateTerminalClaim extends Model
{
    protected $fillable = [
        'gate',
        'terminal_token',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
        ];
    }
}
