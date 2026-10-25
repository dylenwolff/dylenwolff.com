<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'client_project_id', 'name', 'status', 'currency', 'monthly_fee', 'started_at', 'next_billing_date', 'ended_at', 'services', 'notes'])]
class MaintenancePlan extends Model
{
    protected function casts(): array
    {
        return ['monthly_fee' => 'decimal:2', 'started_at' => 'date', 'next_billing_date' => 'date', 'ended_at' => 'date'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function clientProject(): BelongsTo
    {
        return $this->belongsTo(ClientProject::class);
    }
}
