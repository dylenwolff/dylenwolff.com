<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'total_sent', 'accepted', 'bounced', 'delayed', 'reputation_blocked',
    'invalid_recipient', 'server_rejected', 'measured_at',
])]
class OutreachDeliveryMetric extends Model
{
    protected function casts(): array
    {
        return ['measured_at' => 'datetime'];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], ['measured_at' => now()]);
    }
}
