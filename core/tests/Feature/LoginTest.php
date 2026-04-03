<?php

namespace Tests\Feature;

use App\Models\User;
use App\Constants\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Login form redirects to home with login panel open (current design).
     */
    public function test_login_page_redirects_to_home(): void
    {
        $response = $this->get(route('user.login'));
        $response->assertRedirect(route('home', ['open' => 'login']));
    }

    /**
     * Invalid credentials return validation/error (redirect back with errors).
     */
    public function test_invalid_login_returns_back_with_errors(): void
    {
        $response = $this->post(route('user.login'), [
            'username' => 'nonexistent',
            'password' => 'wrong',
        ]);
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /**
     * Valid credentials log the user in and redirect.
     * Uses UserFactory with app-specific fields when table has username, firstname, lastname, status, ev, sv.
     */
    public function test_valid_login_redirects_successfully(): void
    {
        $user = User::factory()->create([
            'username'  => 'testuser',
            'email'     => 'test@example.com',
            'firstname' => 'Test',
            'lastname'  => 'User',
            'password'  => bcrypt('password'),
            'status'    => Status::USER_ACTIVE,
            'ev'        => Status::VERIFIED,
            'sv'        => Status::VERIFIED,
        ]);

        $response = $this->post(route('user.login'), [
            'username' => 'testuser',
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }
}
