<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Halaman memakai @vite; di tes tidak perlu manifest hasil build.
        $this->withoutVite();
        RateLimiter::clear('budi@email.com|127.0.0.1');
    }

    private function makeUser(string $email = 'budi@email.com'): User
    {
        return User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/lokasi-ternak')->assertRedirect(route('login'));
        $this->get('/geofence')->assertRedirect(route('login'));
        $this->get('/pengaturan')->assertRedirect(route('login'));
    }

    public function test_login_page_is_shown_to_guests(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk ke akun Anda');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_email_is_case_insensitive(): void
    {
        $user = $this->makeUser();

        $this->post('/login', ['email' => '  Budi@Email.com ', 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->makeUser();

        $this->from('/login')
            ->post('/login', ['email' => 'budi@email.com', 'password' => 'salah-banget'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'Email atau password yang Anda masukkan salah.']);

        $this->assertGuest();
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_user_is_returned_to_the_page_they_wanted(): void
    {
        $this->makeUser();

        $this->get('/lokasi-ternak')->assertRedirect(route('login'));

        $this->post('/login', ['email' => 'budi@email.com', 'password' => 'password123'])
            ->assertRedirect('/lokasi-ternak');
    }

    public function test_logged_in_user_can_open_the_dashboard(): void
    {
        $this->actingAs($this->makeUser())->get('/')->assertOk()->assertSee('Dashboard Monitoring');
    }

    public function test_logged_in_user_is_redirected_away_from_login(): void
    {
        $this->actingAs($this->makeUser())->get('/login')->assertRedirect(route('dashboard'));
    }

    public function test_user_can_logout(): void
    {
        $this->actingAs($this->makeUser())
            ->post('/logout')
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Anda berhasil keluar dari sistem.');

        $this->assertGuest();
    }

    public function test_guest_cannot_post_logout(): void
    {
        $this->post('/logout')->assertRedirect(route('login'));
    }

    public function test_login_is_locked_after_five_failed_attempts(): void
    {
        $this->makeUser();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'budi@email.com', 'password' => 'salah']);
        }

        $response = $this->post('/login', ['email' => 'budi@email.com', 'password' => 'password123']);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_password_can_be_changed_and_used_to_login_again(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->put('/pengaturan/password', [
                'current_password' => 'password123',
                'password' => 'passwordBaru9',
                'password_confirmation' => 'passwordBaru9',
            ])
            ->assertSessionHasNoErrors();

        $this->post('/logout');
        $this->post('/login', ['email' => $user->email, 'password' => 'passwordBaru9'])
            ->assertRedirect(route('dashboard'));
    }

    public function test_password_change_rejects_wrong_current_password(): void
    {
        $this->actingAs($this->makeUser())
            ->put('/pengaturan/password', [
                'current_password' => 'bukan-password-saya',
                'password' => 'passwordBaru9',
                'password_confirmation' => 'passwordBaru9',
            ])
            ->assertSessionHasErrors('current_password');
    }
}
