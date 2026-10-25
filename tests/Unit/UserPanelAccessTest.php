<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserPanelAccessTest extends TestCase
{
    #[Test]
    public function the_approved_email_addresses_can_access_the_admin_panel(): void
    {
        foreach (['dylenaw@outlook.com', 'dylenaw@gmail.com', 'connect@dylenwolff.com'] as $email) {
            $this->assertTrue((new User(['email' => $email]))->canAccessWorkspace());
        }
    }

    #[Test]
    public function other_users_cannot_access_the_admin_panel(): void
    {
        $user = new User(['email' => 'someone@example.com']);
        $this->assertFalse($user->canAccessWorkspace());
    }
}
