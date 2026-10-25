<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'contact_id', 'client_project_id', 'number', 'status', 'currency', 'total', 'amount_paid', 'issued_at', 'due_at', 'paid_at', 'notes'])]
class Invoice extends Model
{
    protected function casts(): array
    {
        return ['total' => 'decimal:2', 'amount_paid' => 'decimal:2', 'issued_at' => 'date', 'due_at' => 'date', 'paid_at' => 'datetime'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function clientProject(): BelongsTo
    {
        return $this->belongsTo(ClientProject::class);
    }
}
