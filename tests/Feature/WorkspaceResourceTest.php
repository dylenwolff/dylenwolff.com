<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\OutreachEmail;
use App\Models\User;
use App\Support\WhatsAppLink;
use App\Support\WorkspaceResources;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WorkspaceResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_native_daisy_workspace_lists_and_creates_organizations(): void
    {
        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();

        $this->actingAs($user)
            ->get('http://admin.dylenwolff.com/manage/organizations')
            ->assertOk()
            ->assertSee('Add organization')
            ->assertDontSee('Filament');

        $this->actingAs($user)
            ->post('http://admin.dylenwolff.com/manage/organizations', [
                'name' => 'New Zealand Test Client',
                'country_code' => 'NZ',
                'status' => 'prospect',
            ])
            ->assertRedirect('http://admin.dylenwolff.com/manage/organizations');

        $this->assertDatabaseHas('organizations', ['name' => 'New Zealand Test Client', 'country_code' => 'NZ']);
    }

    public function test_every_native_management_section_loads(): void
    {
        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();

        foreach (array_keys(WorkspaceResources::all()) as $section) {
            $response = $this->actingAs($user)->get("http://admin.dylenwolff.com/manage/{$section}");
            $response->assertStatus(in_array($response->status(), [200, 302], true) ? $response->status() : 200);
        }
    }

    public function test_old_filament_urls_redirect_to_the_native_workspace(): void
    {
        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();

        $this->actingAs($user)
            ->get('http://admin.dylenwolff.com/legacy/organizations')
            ->assertRedirect('http://admin.dylenwolff.com/manage/organizations');
    }

    public function test_an_approved_outreach_email_can_be_sent_and_tracked(): void
    {
        Mail::fake();

        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();
        $organization = Organization::create(['name' => 'Example Client', 'status' => 'prospect']);
        $contact = Contact::create([
            'organization_id' => $organization->id,
            'first_name' => 'Alex',
            'email' => 'alex@example.com',
        ]);
        $lead = Lead::create([
            'organization_id' => $organization->id,
            'contact_id' => $contact->id,
            'title' => 'Website redevelopment',
            'status' => 'new',
        ]);
        $email = OutreachEmail::create([
            'organization_id' => $organization->id,
            'contact_id' => $contact->id,
            'lead_id' => $lead->id,
            'recipient_name' => 'Alex',
            'recipient_email' => 'alex@example.com',
            'subject' => 'A simpler website workflow',
            'body' => 'Hello Alex, I would like to share a practical idea for your website.',
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->post("http://admin.dylenwolff.com/outreach-emails/{$email->id}/send")
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('outreach_emails', ['id' => $email->id, 'status' => 'sent']);
        $this->assertDatabaseHas('activities', ['lead_id' => $lead->id, 'type' => 'email']);
        $this->assertDatabaseHas('crm_tasks', ['lead_id' => $lead->id, 'status' => 'todo']);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'contacted']);
    }

    public function test_a_draft_or_already_sent_email_cannot_be_sent(): void
    {
        Mail::fake();
        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();
        $email = OutreachEmail::create([
            'recipient_email' => 'alex@example.com',
            'subject' => 'Draft email',
            'body' => 'This message is still being reviewed.',
            'status' => 'draft',
        ]);

        $this->actingAs($user)
            ->post("http://admin.dylenwolff.com/outreach-emails/{$email->id}/send")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('outreach_emails', ['id' => $email->id, 'status' => 'draft', 'sent_at' => null]);
        $this->assertDatabaseCount('activities', 0);
    }

    public function test_whatsapp_link_uses_the_organization_country_and_tracks_the_opened_draft(): void
    {
        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();
        $organization = Organization::create([
            'name' => 'Australian Test Client',
            'country_code' => 'AU',
            'phone' => '0412 345 678',
            'status' => 'prospect',
        ]);
        $lead = Lead::create([
            'organization_id' => $organization->id,
            'title' => 'improve website enquiries',
            'status' => 'new',
        ]);

        $lead->load('organization');
        $this->assertSame('61412345678', WhatsAppLink::phone($lead));

        $this->actingAs($user)
            ->post("http://admin.dylenwolff.com/leads/{$lead->id}/whatsapp")
            ->assertRedirectContains('https://wa.me/61412345678?text=');

        $this->assertDatabaseHas('activities', [
            'lead_id' => $lead->id,
            'type' => 'whatsapp',
            'subject' => 'WhatsApp draft opened',
        ]);
    }
}
