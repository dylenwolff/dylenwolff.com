<?php

namespace Tests\Feature;

use App\Models\ClientDocument;
use App\Models\ClientProject;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\MaintenancePlan;
use App\Models\Organization;
use App\Models\Quote;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_records_are_connected_to_an_organization(): void
    {
        $organization = Organization::create(['name' => 'Example Client', 'status' => 'client']);
        $contact = Contact::create(['organization_id' => $organization->id, 'first_name' => 'Alex']);
        $project = ClientProject::create([
            'organization_id' => $organization->id,
            'contact_id' => $contact->id,
            'name' => 'Business website',
            'currency' => 'LKR',
            'fee' => 150000,
        ]);

        Quote::create(['organization_id' => $organization->id, 'number' => 'Q-001', 'total' => 150000]);
        Invoice::create(['organization_id' => $organization->id, 'client_project_id' => $project->id, 'number' => 'INV-001', 'total' => 75000]);
        MaintenancePlan::create(['organization_id' => $organization->id, 'client_project_id' => $project->id, 'name' => 'Care plan', 'monthly_fee' => 15000]);
        SupportTicket::create(['organization_id' => $organization->id, 'client_project_id' => $project->id, 'subject' => 'Content update']);
        ClientDocument::create(['organization_id' => $organization->id, 'client_project_id' => $project->id, 'title' => 'Project brief']);

        $this->assertCount(1, $organization->fresh()->clientProjects);
        $this->assertCount(1, $organization->quotes);
        $this->assertCount(1, $organization->invoices);
        $this->assertCount(1, $organization->maintenancePlans);
        $this->assertCount(1, $organization->supportTickets);
        $this->assertCount(1, $organization->documents);
    }
}
