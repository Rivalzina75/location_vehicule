<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_payment_methods_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.payment-methods'));
        $response->assertStatus(200);
    }

    public function test_user_can_add_first_payment_method_as_default(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.payment-methods.store'), [
            'card_holder_name' => 'Jean Dupont',
            'card_number' => '4111111111111111',
            'card_brand' => 'visa',
            'expiry_month' => '12',
            'expiry_year' => (string) (now()->year + 1),
        ]);

        $response->assertRedirect(route('dashboard.payment-methods'));
        $this->assertDatabaseHas('payment_methods', [
            'user_id' => $this->user->id,
            'card_brand' => 'visa',
            'card_last_four' => '1111',
            'is_default' => true,
        ]);
    }

    public function test_user_can_set_default_payment_method_on_create(): void
    {
        PaymentMethod::create([
            'user_id' => $this->user->id,
            'card_brand' => 'visa',
            'card_last_four' => '4242',
            'card_holder_name' => 'Jean Dupont',
            'expiry_month' => '11',
            'expiry_year' => (string) (now()->year + 2),
            'is_default' => true,
        ]);

        $this->actingAs($this->user)->post(route('dashboard.payment-methods.store'), [
            'card_holder_name' => 'Marie Martin',
            'card_number' => '5555555555554444',
            'card_brand' => 'mastercard',
            'expiry_month' => '10',
            'expiry_year' => (string) (now()->year + 3),
            'is_default' => '1',
        ]);

        $this->assertDatabaseHas('payment_methods', [
            'user_id' => $this->user->id,
            'card_last_four' => '4444',
            'is_default' => true,
        ]);
        $this->assertDatabaseHas('payment_methods', [
            'user_id' => $this->user->id,
            'card_last_four' => '4242',
            'is_default' => false,
        ]);
    }

    public function test_user_can_change_default_payment_method(): void
    {
        $cardOne = PaymentMethod::create([
            'user_id' => $this->user->id,
            'card_brand' => 'visa',
            'card_last_four' => '4242',
            'card_holder_name' => 'Jean Dupont',
            'expiry_month' => '11',
            'expiry_year' => (string) (now()->year + 2),
            'is_default' => true,
        ]);
        $cardTwo = PaymentMethod::create([
            'user_id' => $this->user->id,
            'card_brand' => 'mastercard',
            'card_last_four' => '4444',
            'card_holder_name' => 'Marie Martin',
            'expiry_month' => '10',
            'expiry_year' => (string) (now()->year + 3),
            'is_default' => false,
        ]);

        $this->actingAs($this->user)->patch(route('dashboard.payment-methods.default', $cardTwo->id));

        $this->assertDatabaseHas('payment_methods', [
            'id' => $cardOne->id,
            'is_default' => false,
        ]);
        $this->assertDatabaseHas('payment_methods', [
            'id' => $cardTwo->id,
            'is_default' => true,
        ]);
    }

    public function test_deleting_default_card_sets_next_one_as_default(): void
    {
        $cardOne = PaymentMethod::create([
            'user_id' => $this->user->id,
            'card_brand' => 'visa',
            'card_last_four' => '4242',
            'card_holder_name' => 'Jean Dupont',
            'expiry_month' => '11',
            'expiry_year' => (string) (now()->year + 2),
            'is_default' => true,
        ]);
        $cardTwo = PaymentMethod::create([
            'user_id' => $this->user->id,
            'card_brand' => 'mastercard',
            'card_last_four' => '4444',
            'card_holder_name' => 'Marie Martin',
            'expiry_month' => '10',
            'expiry_year' => (string) (now()->year + 3),
            'is_default' => false,
        ]);

        $this->actingAs($this->user)->delete(route('dashboard.payment-methods.destroy', $cardOne->id));

        $this->assertDatabaseMissing('payment_methods', ['id' => $cardOne->id]);
        $this->assertDatabaseHas('payment_methods', [
            'id' => $cardTwo->id,
            'is_default' => true,
        ]);
    }
}
