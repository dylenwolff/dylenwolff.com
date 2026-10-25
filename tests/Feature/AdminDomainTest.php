<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminDomainTest extends TestCase
{
    public function test_admin_subdomain_redirects_guests_to_the_cms_login(): void
    {
        $response = $this->get('http://admin.dylenwolff.com/');

        $response->assertRedirect('http://admin.dylenwolff.com/login');
    }

    public function test_cms_is_not_exposed_under_the_public_website_admin_path(): void
    {
        $response = $this->get('http://dylenwolff.com/admin');

        $response->assertNotFound();
    }

    public function test_admin_password_reset_is_disabled(): void
    {
        $response = $this->get('http://admin.dylenwolff.com/password-reset/request');

        $response->assertNotFound();
    }

    public function test_admin_login_uses_an_email_code_instead_of_a_password(): void
    {
        $response = $this->get('http://admin.dylenwolff.com/login');

        $response
            ->assertOk()
            ->assertSee('Sign in with an email code')
            ->assertSee('Send login code')
            ->assertDontSee('Forgot password');
    }
}
