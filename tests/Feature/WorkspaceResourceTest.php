<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        foreach (array_keys(\App\Support\WorkspaceResources::all()) as $section) {
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
}
