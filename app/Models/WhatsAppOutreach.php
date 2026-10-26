<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'contact_id', 'lead_id', 'crm_task_id', 'recipient_name', 'phone', 'message', 'status', 'opened_at', 'sent_at', 'notes'])]
class WhatsAppOutreach extends Model
{
    protected $table = 'whatsapp_outreaches';

    protected function casts(): array
    {
        return ['opened_at' => 'datetime', 'sent_at' => 'datetime'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(CrmTask::class, 'crm_task_id');
    }
}
