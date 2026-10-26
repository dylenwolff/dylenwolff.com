<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\CrmTask;
use App\Models\Lead;
use App\Models\WhatsAppOutreach;
use App\Support\WhatsAppLink;
use Illuminate\Http\RedirectResponse;

class WhatsAppOutreachController extends Controller
{
    public function open(Lead $lead): RedirectResponse
    {
        $lead->loadMissing(['contact', 'organization']);
        $fallbackReason = WhatsAppLink::fallbackReason($lead);

        if (! $fallbackReason) {
            return back()->with('warning', 'Email is available for this lead and remains the primary contact route.');
        }

        $url = WhatsAppLink::url($lead);

        if (! $url) {
            return back()->with('warning', 'This lead does not have a usable WhatsApp number yet.');
        }

        $outreach = WhatsAppOutreach::query()->firstOrCreate(
            ['lead_id' => $lead->id, 'phone' => WhatsAppLink::phone($lead)],
            [
                'organization_id' => $lead->organization_id,
                'contact_id' => $lead->contact_id,
                'crm_task_id' => $lead->tasks()->whereNotIn('status', ['done', 'completed', 'cancelled'])->latest()->value('id'),
                'recipient_name' => $lead->contact?->full_name ?: $lead->organization?->name,
                'message' => WhatsAppLink::message($lead),
                'status' => 'draft',
                'notes' => $fallbackReason,
            ],
        );

        if ($outreach->status === 'unavailable') {
            return back()->with('warning', 'This number was already marked as not available on WhatsApp.');
        }

        $outreach->update(['status' => 'opened', 'opened_at' => now(), 'message' => WhatsAppLink::message($lead)]);

        Activity::create([
            'contact_id' => $lead->contact_id,
            'lead_id' => $lead->id,
            'type' => 'whatsapp',
            'subject' => 'WhatsApp draft opened',
            'details' => 'Opened WhatsApp for +'.WhatsAppLink::phone($lead).". Confirm the message was sent before treating this as contacted.\n\n".WhatsAppLink::message($lead),
            'occurred_at' => now(),
        ]);

        return redirect()->away($url);
    }

    public function openRecord(WhatsAppOutreach $whatsappOutreach): RedirectResponse
    {
        return $this->open($whatsappOutreach->lead()->firstOrFail());
    }

    public function markSent(WhatsAppOutreach $whatsappOutreach): RedirectResponse
    {
        if (! $whatsappOutreach->lead || ! WhatsAppLink::fallbackReason($whatsappOutreach->lead)) {
            return back()->with('warning', 'Email is available for this lead and remains the primary contact route.');
        }

        $whatsappOutreach->update(['status' => 'sent', 'sent_at' => now()]);
        $whatsappOutreach->lead?->update(['status' => 'contacted', 'next_follow_up_at' => now()->addDays(4)]);
        Activity::create(['contact_id' => $whatsappOutreach->contact_id, 'lead_id' => $whatsappOutreach->lead_id, 'type' => 'whatsapp', 'subject' => 'WhatsApp message sent', 'details' => 'Confirmed sent to +'.$whatsappOutreach->phone.'.', 'occurred_at' => now()]);
        CrmTask::firstOrCreate(['lead_id' => $whatsappOutreach->lead_id, 'title' => 'Follow up on WhatsApp message'], ['status' => 'todo', 'priority' => 'normal', 'due_at' => now()->addDays(4)]);

        return back()->with('success', 'WhatsApp message marked as sent and follow-up scheduled.');
    }

    public function markUnavailable(WhatsAppOutreach $whatsappOutreach): RedirectResponse
    {
        $whatsappOutreach->update(['status' => 'unavailable', 'notes' => trim(($whatsappOutreach->notes ? $whatsappOutreach->notes."\n" : '').'Number is not registered on WhatsApp. Confirmed '.now()->toDateString().'.')]);
        Activity::create(['contact_id' => $whatsappOutreach->contact_id, 'lead_id' => $whatsappOutreach->lead_id, 'type' => 'whatsapp', 'subject' => 'Number not on WhatsApp', 'details' => '+'.$whatsappOutreach->phone.' was marked unavailable on WhatsApp.', 'occurred_at' => now()]);

        return back()->with('warning', 'Number marked as not available on WhatsApp.');
    }
}
