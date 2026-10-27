<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\CrmTask;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\OutreachEmail;
use App\Models\User;
use App\Support\PhoneNumber;
use App\Support\WhatsAppLink;
use App\Support\WorkspaceResources;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WorkspaceResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_dashboard_loads_for_an_approved_user(): void
    {
        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();

        $this->actingAs($user)
            ->get('http://admin.dylenwolff.com/')
            ->assertOk()
            ->assertSee('Outreach report');
    }

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

        $organization = Organization::query()->where('name', 'New Zealand Test Client')->firstOrFail();
        $this->actingAs($user)
            ->get("http://admin.dylenwolff.com/manage/organizations/{$organization->id}")
            ->assertOk()
            ->assertSee('New Zealand Test Client')
            ->assertSee('Edit organization');
    }

    public function test_every_native_management_section_loads(): void
    {
        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();

        foreach (array_keys(WorkspaceResources::all()) as $section) {
            $response = $this->actingAs($user)->get("http://admin.dylenwolff.com/manage/{$section}");
            $response->assertStatus(in_array($response->status(), [200, 302], true) ? $response->status() : 200);
        }
    }

    public function test_tasks_are_ordered_by_work_state_priority_and_due_date(): void
    {
        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();
        CrmTask::create(['title' => 'Completed task', 'status' => 'completed', 'priority' => 'urgent', 'due_at' => now()->subDay()]);
        CrmTask::create(['title' => 'Normal task', 'status' => 'todo', 'priority' => 'normal', 'due_at' => now()]);
        CrmTask::create(['title' => 'Urgent task', 'status' => 'todo', 'priority' => 'urgent', 'due_at' => now()->addDay()]);
        CrmTask::create(['title' => 'Work in progress', 'status' => 'in_progress', 'priority' => 'normal', 'due_at' => now()->addDays(2)]);

        $this->actingAs($user)
            ->get('http://admin.dylenwolff.com/manage/crm-tasks')
            ->assertOk()
            ->assertSeeInOrder(['Work in progress', 'Urgent task', 'Normal task', 'Completed task']);
    }

    public function test_task_list_shows_the_organization_contact_and_phone(): void
    {
        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();
        $organization = Organization::create(['name' => 'Clear Call Client', 'country_code' => 'AU', 'phone' => '0411 222 333']);
        $contact = Contact::create(['organization_id' => $organization->id, 'first_name' => 'Jamie', 'last_name' => 'Owner']);
        $lead = Lead::create(['organization_id' => $organization->id, 'contact_id' => $contact->id, 'title' => 'Website upgrade']);
        CrmTask::create(['contact_id' => $contact->id, 'lead_id' => $lead->id, 'title' => 'Call Clear Call Client', 'status' => 'todo']);

        $this->actingAs($user)
            ->get('http://admin.dylenwolff.com/manage/crm-tasks')
            ->assertOk()
            ->assertSee('Clear Call Client')
            ->assertSee('Jamie Owner')
            ->assertSee('+61411222333');

        $this->assertSame('+61411222333', PhoneNumber::international('0411 222 333', 'AU'));
        $this->assertSame('+61885363948', PhoneNumber::international('(08) 8536 3948', 'AU'));
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
        OutreachEmail::create([
            'organization_id' => $organization->id,
            'lead_id' => $lead->id,
            'recipient_email' => 'old@example.com',
            'subject' => 'Earlier message',
            'body' => 'Earlier outreach message.',
            'status' => 'bounced',
        ]);

        $lead->load('organization');
        $this->assertSame('61412345678', WhatsAppLink::phone($lead));
        $this->assertStringContainsString('my message could not be delivered', WhatsAppLink::message($lead));

        $this->actingAs($user)
            ->post("http://admin.dylenwolff.com/leads/{$lead->id}/whatsapp")
            ->assertRedirectContains('https://wa.me/61412345678?text=');

        $this->assertDatabaseHas('activities', [
            'lead_id' => $lead->id,
            'type' => 'whatsapp',
            'subject' => 'WhatsApp draft opened',
        ]);

        $organization->update(['phone' => '(08) 8981 1212']);
        $lead->refresh()->load('organization');
        $this->assertNull(WhatsAppLink::phone($lead));
    }

    public function test_whatsapp_is_not_offered_when_a_working_email_is_available(): void
    {
        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();
        $organization = Organization::create(['name' => 'Email First Client', 'country_code' => 'AU', 'phone' => '0412 345 678', 'email' => 'hello@example.com', 'status' => 'prospect']);
        $lead = Lead::create(['organization_id' => $organization->id, 'title' => 'improve enquiries', 'status' => 'new']);

        $this->assertNull(WhatsAppLink::fallbackReason($lead));
        $this->assertNull(WhatsAppLink::url($lead));

        $this->actingAs($user)->post("http://admin.dylenwolff.com/leads/{$lead->id}/whatsapp")->assertRedirect();
        $this->assertDatabaseMissing('whatsapp_outreaches', ['lead_id' => $lead->id]);
    }
}
