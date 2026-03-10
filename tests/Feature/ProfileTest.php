<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'password' => Hash::make('MonSuperMdp@2025'),
        ]);
    }

    // ───────── View ─────────

    public function test_profile_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.profile.show'));
        $response->assertStatus(200);
        $response->assertSee('Jean');
        $response->assertSee('Dupont');
    }

    public function test_profile_edit_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.profile.edit'));
        $response->assertStatus(200);
    }

    // ───────── Update Personal Info ─────────

    public function test_user_can_update_name(): void
    {
        $response = $this->actingAs($this->user)->put(route('dashboard.profile.update'), [
            'first_name' => 'Marie',
            'last_name' => 'Martin',
            'email' => 'jean@example.com',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $response->assertRedirect(route('dashboard.profile.show'));
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'first_name' => 'Marie',
            'last_name' => 'Martin',
        ]);
    }

    public function test_user_can_update_email(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->user)->put(route('dashboard.profile.update'), [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'newemail@example.com',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'email' => 'newemail@example.com']);
        $this->assertNull($this->user->fresh()->email_verified_at);
        Notification::assertSentTo($this->user->fresh(), VerifyEmail::class);
    }

    public function test_update_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($this->user)->put(route('dashboard.profile.update'), [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'existing@example.com',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_update_fails_with_invalid_postal_code(): void
    {
        $response = $this->actingAs($this->user)->put(route('dashboard.profile.update'), [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => 'ABCDE',
            'city' => 'Paris',
        ]);

        $response->assertSessionHasErrors('postal_code');
    }

    public function test_update_fails_with_xss_in_name(): void
    {
        $response = $this->actingAs($this->user)->put(route('dashboard.profile.update'), [
            'first_name' => '<script>alert("xss")</script>',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'date_of_birth' => '15/03/1990',
            'phone_number' => '06 12 34 56 78',
            'address_line1' => '12 rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $response->assertSessionHasErrors('first_name');
    }

    // ───────── Password ─────────

    public function test_user_can_update_password(): void
    {
        $response = $this->actingAs($this->user)->put(route('dashboard.profile.password.update'), [
            'current_password' => 'MonSuperMdp@2025',
            'password' => 'NouveauMdp@@2025',
            'password_confirmation' => 'NouveauMdp@@2025',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('NouveauMdp@@2025', $this->user->fresh()->password));
    }

    public function test_password_update_fails_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->user)->put(route('dashboard.profile.password.update'), [
            'current_password' => 'WrongPassword@123',
            'password' => 'NouveauMdp@@2025',
            'password_confirmation' => 'NouveauMdp@@2025',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_password_update_fails_with_same_password(): void
    {
        $response = $this->actingAs($this->user)->put(route('dashboard.profile.password.update'), [
            'current_password' => 'MonSuperMdp@2025',
            'password' => 'MonSuperMdp@2025',
            'password_confirmation' => 'MonSuperMdp@2025',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_update_fails_with_weak_password(): void
    {
        $response = $this->actingAs($this->user)->put(route('dashboard.profile.password.update'), [
            'current_password' => 'MonSuperMdp@2025',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_update_fails_without_confirmation(): void
    {
        $response = $this->actingAs($this->user)->put(route('dashboard.profile.password.update'), [
            'current_password' => 'MonSuperMdp@2025',
            'password' => 'NouveauMdp@@2025',
            'password_confirmation' => 'DifferentPassword@2025',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ───────── Delete Account ─────────

    public function test_user_can_delete_account(): void
    {
        $response = $this->actingAs($this->user)->delete(route('dashboard.profile.destroy'), [
            'password' => 'MonSuperMdp@2025',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    public function test_delete_fails_with_wrong_password(): void
    {
        $response = $this->actingAs($this->user)->delete(route('dashboard.profile.destroy'), [
            'password' => 'WrongPassword@123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }

    public function test_delete_fails_with_active_reservations(): void
    {
        // Create an active reservation
        $vehicle = \App\Models\Vehicle::create([
            'brand' => 'Toyota',
            'model' => 'Yaris',
            'type' => 'citycar',
            'year' => 2024,
            'registration_number' => 'AA-123-BB',
            'transmission' => 'manual',
            'fuel_type' => 'gasoline',
            'seats' => 5,
            'price_per_day' => 45,
            'status' => 'available',
        ]);
        \App\Models\Reservation::create([
            'user_id' => $this->user->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => today(),
            'end_date' => today()->addDays(5),
            'duration_days' => 5,
            'base_price' => 225,
            'total_price' => 225,
            'status' => 'active',
            'payment_status' => 'completed',
        ]);

        $response = $this->actingAs($this->user)->delete(route('dashboard.profile.destroy'), [
            'password' => 'MonSuperMdp@2025',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }
}
