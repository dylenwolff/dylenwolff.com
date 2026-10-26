<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactMessage;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Qualification;
use App\Support\ContactSpamGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PortfolioCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_content_from_the_cms(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('digital solutions that work.')
            ->assertSee('Professional websites')
            ->assertSee('Send enquiry');
    }

    public function test_an_enquiry_is_saved_and_a_notification_is_sent(): void
    {
        Mail::fake();

        $response = $this->post('/contact', [
            'name' => 'Example Client',
            'email' => 'client@example.com',
            'subject' => 'New website',
            'message' => 'I would like to discuss a new website for my business.',
            'website' => '',
            'form_started_at' => Crypt::encryptString((string) now()->subSeconds(5)->timestamp),
        ]);

        $response->assertRedirect()->assertSessionHas('contact_success');
        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'client@example.com',
            'subject' => 'New website',
            'status' => 'new',
        ]);
        Mail::assertSentCount(1);
        $this->assertDatabaseHas(Contact::class, ['email' => 'client@example.com']);
        $this->assertDatabaseHas(Lead::class, [
            'source' => 'website',
            'status' => 'new',
            'title' => 'New website',
        ]);
        $this->assertDatabaseHas(ContactMessage::class, ['email' => 'client@example.com']);
    }

    public function test_portfolio_pages_and_project_case_studies_are_accessible(): void
    {
        $project = Project::query()->firstOrFail();

        $this->get('/work')->assertOk()->assertSee($project->title);
        $this->get(route('work.show', $project))->assertOk()->assertSee($project->title);
        $this->get('/about')->assertOk();
        $this->get('/services')->assertOk();
        $this->get('/contact')->assertOk()->assertSee('Send enquiry');
        $this->get('/privacy')->assertOk()->assertSee('What is collected');
    }

    public function test_published_qualifications_appear_on_the_about_page(): void
    {
        $qualification = Qualification::query()->firstOrFail();

        $this->get('/about')
            ->assertOk()
            ->assertSee('Qualifications')
            ->assertSee($qualification->title)
            ->assertSee($qualification->institution);
    }

    public function test_the_honeypot_rejects_automated_submissions(): void
    {
        $this->post('/contact', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'This automated message is definitely long enough.',
            'website' => 'spam.example',
            'form_started_at' => Crypt::encryptString((string) now()->subSeconds(5)->timestamp),
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_suspicious_contact_content_is_quarantined_without_creating_a_lead(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name' => 'Spam Bot',
            'email' => 'bot@example.top',
            'subject' => 'Casino promotion',
            'message' => 'Visit <a href=https://spam.example.top>casino bonus backlinks</a> today.',
            'website' => '',
            'form_started_at' => Crypt::encryptString((string) now()->subSeconds(5)->timestamp),
        ])->assertRedirect()->assertSessionHas('contact_success');

        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'bot@example.top',
            'status' => 'spam',
            'lead_id' => null,
        ]);
        $this->assertDatabaseCount('leads', 0);
        $this->assertDatabaseCount('contacts', 0);
        Mail::assertNothingSent();
    }

    public function test_an_impossibly_fast_submission_is_quarantined(): void
    {
        $this->post('/contact', [
            'name' => 'Fast Bot',
            'email' => 'bot@example.com',
            'subject' => 'A project',
            'message' => 'This submission was completed faster than a person could type it.',
            'website' => '',
            'form_started_at' => ContactSpamGuard::formToken(),
        ])->assertRedirect();

        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'bot@example.com',
            'status' => 'spam',
        ]);
        $this->assertDatabaseCount('leads', 0);
    }
}
