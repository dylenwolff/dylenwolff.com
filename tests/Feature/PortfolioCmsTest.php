<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        ]);

        $response->assertRedirect()->assertSessionHas('contact_success');
        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'client@example.com',
            'subject' => 'New website',
            'status' => 'new',
        ]);
        Mail::assertSentCount(1);
    }

    public function test_portfolio_pages_and_project_case_studies_are_accessible(): void
    {
        $project = Project::query()->firstOrFail();

        $this->get('/work')->assertOk()->assertSee($project->title);
        $this->get(route('work.show', $project))->assertOk()->assertSee($project->title);
        $this->get('/about')->assertOk();
        $this->get('/services')->assertOk();
        $this->get('/contact')->assertOk()->assertSee('Send enquiry');
    }

    public function test_the_honeypot_rejects_automated_submissions(): void
    {
        $this->post('/contact', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'This automated message is definitely long enough.',
            'website' => 'spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }
}
