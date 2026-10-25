<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'contact_id', 'opportunity_id', 'number', 'status', 'currency', 'subtotal', 'tax', 'total', 'issued_at', 'valid_until', 'scope', 'notes'])]
class Quote extends Model
{
    protected function casts(): array
    {
        return ['subtotal' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2', 'issued_at' => 'date', 'valid_until' => 'date'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }
}
