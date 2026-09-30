<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('welcome', [
            'settings' => SiteSetting::current(),
            'services' => Service::query()->where('is_published', true)->orderBy('sort_order')->get(),
            'projects' => Project::query()->where('is_published', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function contact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'website' => ['nullable', 'max:0'],
        ]);

        $contact = ContactMessage::create($validated);

        try {
            Mail::raw(
                "New website enquiry from {$contact->name} <{$contact->email}>\n\nSubject: " . ($contact->subject ?: 'General enquiry') . "\n\n{$contact->message}",
                fn ($mail) => $mail->to(SiteSetting::current()->email)
                    ->replyTo($contact->email, $contact->name)
                    ->subject('Website enquiry: ' . ($contact->subject ?: $contact->name)),
            );
        } catch (Throwable $exception) {
            Log::error('Contact notification could not be sent.', ['contact_message_id' => $contact->id, 'exception' => $exception]);
        }

        return back()->with('contact_success', 'Thank you—your message has been received. I’ll get back to you soon.');
    }
}
