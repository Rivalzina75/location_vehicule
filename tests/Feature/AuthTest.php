<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ───────── Registration ─────────

    public function test_registration_page_is_accessible(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'password' => 'MonSuperMdp@2025',
            'password_confirmation' => 'MonSuperMdp@2025',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'jean@example.com']);
    }

    public function test_registration_fails_with_weak_password(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'jean@example.com']);
    }

    public function test_registration_fails_with_password_no_uppercase(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'password' => 'monsupermotdepasse@2025',
            'password_confirmation' => 'monsupermotdepasse@2025',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_fails_with_password_no_special_char(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'password' => 'MonSuperMdp20255',
            'password_confirmation' => 'MonSuperMdp20255',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_fails_with_password_no_number(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'password' => 'MonSuperMotDePasse@',
            'password_confirmation' => 'MonSuperMotDePasse@',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'existing@example.com',
            'password' => 'MonSuperMdp@2025',
            'password_confirmation' => 'MonSuperMdp@2025',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ───────── Login ─────────

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('MonSuperMdp@2025'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'MonSuperMdp@2025',
        ]);

        $this->assertAuthenticated();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('MonSuperMdp@2025'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPassword@123',
        ]);

        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'SomePassword@123',
        ]);

        $this->assertGuest();
    }

    public function test_login_fails_with_sql_injection_attempt(): void
    {
        $response = $this->post('/login', [
            'email' => "admin@example.com' OR '1'='1",
            'password' => "password' OR '1'='1",
        ]);

        $this->assertGuest();
    }

    public function test_login_fails_with_xss_in_email(): void
    {
        $response = $this->post('/login', [
            'email' => '<script>alert("xss")</script>',
            'password' => 'SomePassword@123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    // ───────── Logout ─────────

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
    }

    // ───────── Auth Guards ─────────

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_unverified_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/email/verify');
    }

    public function test_verified_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_client_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
    }
}
