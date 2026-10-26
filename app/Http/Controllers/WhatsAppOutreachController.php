<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Lead;
use App\Support\WhatsAppLink;
use Illuminate\Http\RedirectResponse;

class WhatsAppOutreachController extends Controller
{
    public function open(Lead $lead): RedirectResponse
    {
        $lead->loadMissing(['contact', 'organization']);
        $url = WhatsAppLink::url($lead);

        if (! $url) {
            return back()->with('warning', 'This lead does not have a usable WhatsApp number yet.');
        }

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
}
