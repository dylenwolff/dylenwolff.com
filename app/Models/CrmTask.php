<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['contact_id', 'lead_id', 'opportunity_id', 'title', 'description', 'status', 'priority', 'due_at', 'completed_at'])]
class CrmTask extends Model
{
    protected $appends = ['organization_name', 'contact_name', 'contact_phone'];

    protected function casts(): array
    {
        return ['due_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    protected function organizationName(): Attribute
    {
        return Attribute::get(fn () => $this->lead?->organization?->name ?? $this->opportunity?->organization?->name);
    }

    protected function contactName(): Attribute
    {
        return Attribute::get(fn () => $this->contact?->full_name);
    }

    protected function contactPhone(): Attribute
    {
        return Attribute::get(fn () => $this->contact?->phone
            ?: $this->lead?->organization?->phone
            ?: $this->opportunity?->organization?->phone);
    }
}
