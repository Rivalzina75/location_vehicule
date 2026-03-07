<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        // Add a valid payment method to the user for reservation tests
        $this->user->paymentMethods()->create([
            'card_brand' => 'visa',
            'card_last_four' => '4242',
            'card_holder_name' => 'Test User',
            'expiry_month' => '12',
            'expiry_year' => (string) (now()->year + 1),
            'is_default' => true,
        ]);

        $this->vehicle = Vehicle::create([
            'brand' => 'Toyota',
            'model' => 'Yaris',
            'type' => 'citycar',
            'year' => 2024,
            'registration_number' => 'AA-123-BB',
            'transmission' => 'manual',
            'fuel_type' => 'gasoline',
            'seats' => 5,
            'doors' => 5,
            'price_per_day' => 45.00,
            'deposit' => 500,
            'gps_available' => true,
            'child_seat_available' => true,
            'status' => 'available',
        ]);
    }

    // ───────── Index ─────────

    public function test_reservations_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.reservations'));
        $response->assertStatus(200);
    }

    public function test_reservations_page_shows_user_reservations(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(5),
            'end_date' => today()->addDays(10),
            'duration_days' => 5,
            'base_price' => 225,
            'total_price' => 225,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard.reservations'));
        $response->assertSee($reservation->confirmation_code);
    }

    public function test_user_cannot_see_other_users_reservations(): void
    {
        $otherUser = User::factory()->create();
        $reservation = Reservation::create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(5),
            'end_date' => today()->addDays(10),
            'duration_days' => 5,
            'base_price' => 225,
            'total_price' => 225,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard.reservations'));
        $response->assertDontSee($reservation->confirmation_code);
    }

    // ───────── Create ─────────

    public function test_reservation_create_form_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.reservation.create'));
        $response->assertStatus(200);
    }

    public function test_user_can_create_reservation(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDay()->format('Y-m-d'),
            'end_date' => today()->addDays(4)->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'pending',
        ]);
    }

    public function test_reservation_generates_confirmation_code(): void
    {
        $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDay()->format('Y-m-d'),
            'end_date' => today()->addDays(4)->format('Y-m-d'),
        ]);

        $reservation = Reservation::first();
        $this->assertNotNull($reservation->confirmation_code);
        $this->assertStringStartsWith('RES-', $reservation->confirmation_code);
    }

    public function test_reservation_calculates_correct_price(): void
    {
        $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDay()->format('Y-m-d'),
            'end_date' => today()->addDays(4)->format('Y-m-d'),
        ]);

        $reservation = Reservation::first();
        $this->assertEquals(3, $reservation->duration_days);
        $this->assertEquals(135.00, $reservation->base_price); // 3 * 45
    }

    public function test_reservation_with_options_adds_costs(): void
    {
        $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDay()->format('Y-m-d'),
            'end_date' => today()->addDays(4)->format('Y-m-d'),
            'gps' => true,
            'child_seat' => true,
            'insurance_full' => true,
        ]);

        $reservation = Reservation::first();
        $days = 3;
        $expected_options = (3 + 5) * $days; // GPS 3€/day + seat 5€/day
        $expected_insurance = 135 * 0.15; // 15% of base
        $this->assertEquals($expected_options, (float) $reservation->options_price);
        $this->assertGreaterThan(0, (float) $reservation->insurance_price);
    }

    public function test_reservation_fails_for_unavailable_vehicle(): void
    {
        $this->vehicle->update(['status' => 'maintenance']);

        $response = $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDay()->format('Y-m-d'),
            'end_date' => today()->addDays(4)->format('Y-m-d'),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('reservations', ['vehicle_id' => $this->vehicle->id]);
    }

    public function test_reservation_fails_for_past_start_date(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->subDay()->format('Y-m-d'),
            'end_date' => today()->addDays(2)->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('start_date');
    }

    public function test_reservation_fails_when_end_before_start(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(5)->format('Y-m-d'),
            'end_date' => today()->addDays(2)->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('end_date');
    }

    public function test_reservation_fails_with_conflicting_dates(): void
    {
        // Create an existing confirmed reservation
        Reservation::create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(5),
            'end_date' => today()->addDays(10),
            'duration_days' => 5,
            'base_price' => 225,
            'total_price' => 225,
            'status' => 'confirmed',
            'payment_status' => 'completed',
        ]);

        // Try to create overlapping reservation
        $response = $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(7)->format('Y-m-d'),
            'end_date' => today()->addDays(12)->format('Y-m-d'),
        ]);

        $response->assertSessionHas('error');
    }

    public function test_reservation_fails_for_nonexistent_vehicle(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => 9999,
            'start_date' => today()->addDay()->format('Y-m-d'),
            'end_date' => today()->addDays(4)->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('vehicle_id');
    }

    // ───────── Show ─────────

    public function test_user_can_view_own_reservation(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(5),
            'end_date' => today()->addDays(10),
            'duration_days' => 5,
            'base_price' => 225,
            'total_price' => 225,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard.reservation.show', $reservation->id));
        $response->assertStatus(200);
    }

    public function test_user_cannot_view_other_users_reservation(): void
    {
        $otherUser = User::factory()->create();
        $reservation = Reservation::create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(5),
            'end_date' => today()->addDays(10),
            'duration_days' => 5,
            'base_price' => 225,
            'total_price' => 225,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard.reservation.show', $reservation->id));
        $response->assertStatus(403);
    }

    // ───────── Cancel ─────────

    public function test_user_can_cancel_pending_reservation(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(5),
            'end_date' => today()->addDays(10),
            'duration_days' => 5,
            'base_price' => 225,
            'total_price' => 225,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->delete(route('dashboard.reservation.destroy', $reservation->id));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'status' => 'cancelled']);
    }

    public function test_user_cannot_cancel_active_reservation(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDays(10),
            'duration_days' => 11,
            'base_price' => 495,
            'total_price' => 495,
            'status' => 'active',
            'payment_status' => 'completed',
        ]);

        $response = $this->actingAs($this->user)->delete(route('dashboard.reservation.destroy', $reservation->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'status' => 'active']);
    }

    public function test_user_cannot_cancel_other_users_reservation(): void
    {
        $otherUser = User::factory()->create();
        $reservation = Reservation::create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(5),
            'end_date' => today()->addDays(10),
            'duration_days' => 5,
            'base_price' => 225,
            'total_price' => 225,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->delete(route('dashboard.reservation.destroy', $reservation->id));
        $response->assertStatus(403);
    }

    // ───────── Admin ─────────

    public function test_admin_can_confirm_reservation(): void
    {
        $admin = User::factory()->admin()->create();
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(5),
            'end_date' => today()->addDays(10),
            'duration_days' => 5,
            'base_price' => 225,
            'total_price' => 225,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.reservations.confirm', $reservation->id));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'status' => 'confirmed']);
    }

    public function test_client_cannot_confirm_reservation(): void
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(5),
            'end_date' => today()->addDays(10),
            'duration_days' => 5,
            'base_price' => 225,
            'total_price' => 225,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->patch(route('admin.reservations.confirm', $reservation->id));
        $response->assertStatus(403);
    }

    // ───────── Activity Log ─────────

    public function test_reservation_creation_logs_activity(): void
    {
        $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDay()->format('Y-m-d'),
            'end_date' => today()->addDays(4)->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'reservation_created',
        ]);
    }

    public function test_reservation_requires_valid_payment_method(): void
    {
        // Create a user without a valid payment method
        $userWithoutPayment = User::factory()->create();

        $response = $this->actingAs($userWithoutPayment)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(3)->format('Y-m-d'),
            'end_date' => today()->addDays(5)->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('dashboard.payment-methods'));
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $userWithoutPayment->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(3)->format('Y-m-d'),
        ]);
    }

    public function test_reservation_is_allowed_with_valid_payment_method(): void
    {
        // User already has a valid payment method from setUp()
        $response = $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(3)->format('Y-m-d'),
            'end_date' => today()->addDays(5)->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->addDays(3)->format('Y-m-d'),
        ]);
    }

    public function test_cancellation_must_be_at_least_3_days_in_advance(): void
    {
        // Create a second vehicle for this test
        $vehicle2 = Vehicle::create([
            'brand' => 'Honda',
            'model' => 'Civic',
            'type' => 'sedan',
            'year' => 2024,
            'registration_number' => 'AA-124-BB',
            'transmission' => 'automatic',
            'fuel_type' => 'hybrid',
            'seats' => 5,
            'doors' => 4,
            'price_per_day' => 50.00,
            'deposit' => 600,
            'gps_available' => true,
            'child_seat_available' => true,
            'status' => 'available',
        ]);

        // Create reservation starting in 2 days via POST
        $startDate = today()->addDays(2)->format('Y-m-d');
        $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $vehicle2->id,
            'start_date' => $startDate,
            'end_date' => today()->addDays(4)->format('Y-m-d'),
        ]);

        $reservation = Reservation::where('user_id', $this->user->id)
            ->where('vehicle_id', $vehicle2->id)
            ->first();

        $response = $this->actingAs($this->user)->delete(route('dashboard.reservation.destroy', $reservation->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'pending',
        ]);
    }

    public function test_cancellation_is_allowed_with_3_days_advance(): void
    {
        // Create a third vehicle for this test
        $vehicle3 = Vehicle::create([
            'brand' => 'BMW',
            'model' => '3 Series',
            'type' => 'sedan',
            'year' => 2024,
            'registration_number' => 'AA-125-BB',
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'seats' => 5,
            'doors' => 4,
            'price_per_day' => 80.00,
            'deposit' => 800,
            'gps_available' => true,
            'child_seat_available' => true,
            'status' => 'available',
        ]);

        // Create reservation starting in 3 days via POST
        $startDate = today()->addDays(3)->format('Y-m-d');
        $this->actingAs($this->user)->post(route('dashboard.reservation.store'), [
            'vehicle_id' => $vehicle3->id,
            'start_date' => $startDate,
            'end_date' => today()->addDays(5)->format('Y-m-d'),
        ]);

        $reservation = Reservation::where('user_id', $this->user->id)
            ->where('vehicle_id', $vehicle3->id)
            ->first();

        // Before deletion, manually set it to confirmed status so it can be cancelled
        $reservation->update(['status' => 'confirmed']);

        $response = $this->actingAs($this->user)->delete(route('dashboard.reservation.destroy', $reservation->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled',
        ]);
    }
}
