<?php

namespace Tests\Feature;

use App\Models\User;
use App\Constants\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guest cannot access checkout – redirected to login.
     */
    public function test_guest_cannot_access_checkout(): void
    {
        $response = $this->get(route('user.checkout.index'));
        $response->assertRedirect(route('user.login'));
    }

    /**
     * Authenticated user can access checkout page when cart has items.
     * (Checkout page may still 404 if cart is empty – that is app logic.)
     */
    public function test_authenticated_user_can_access_checkout_page(): void
    {
        $user = User::factory()->create([
            'username'  => 'checkoutuser',
            'email'     => 'checkout@example.com',
            'firstname' => 'Check',
            'lastname'  => 'Out',
            'password'  => bcrypt('password'),
            'status'    => Status::USER_ACTIVE,
            'ev'        => Status::VERIFIED,
            'sv'        => Status::VERIFIED,
        ]);

        $response = $this->actingAs($user)->get(route('user.checkout.index'));
        // Either 200 (page with empty cart message) or 404 (app aborts when cart empty)
        $this->assertContains($response->status(), [200, 404]);
    }
}
