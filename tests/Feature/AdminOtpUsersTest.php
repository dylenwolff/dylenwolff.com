<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\EmailOtpLogin;
use App\Models\AdminLoginOtp;
use App\Models\User;
use App\Notifications\AdminLoginCode;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class AdminOtpUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_approved_admin_email_accounts_exist(): void
    {
        foreach (['dylenaw@outlook.com', 'dylenaw@gmail.com', 'connect@dylenwolff.com'] as $email) {
            $this->assertTrue(User::query()->where('email', $email)->exists());
        }
    }

    public function test_an_approved_user_can_request_a_login_code(): void
    {
        Notification::fake();
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(EmailOtpLogin::class)
            ->set('data.email', 'dylenaw@gmail.com')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertSet('codeSent', true);

        $user = User::query()->where('email', 'dylenaw@gmail.com')->firstOrFail();

        Notification::assertSentTo($user, AdminLoginCode::class);
        $this->assertDatabaseHas('admin_login_otps', [
            'email' => 'dylenaw@gmail.com',
            'attempts' => 0,
            'used_at' => null,
        ]);
        $this->assertSame(64, strlen(AdminLoginOtp::query()->firstOrFail()->code_hash));
    }
}
