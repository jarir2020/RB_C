<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_guests_are_redirected_to_the_admin_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/admin/login');
    }

    public function test_login_page_renders_as_an_inertia_page(): void
    {
        $this->get('/admin/login')->assertOk()->assertSee('Auth\\/Login', false);
    }

    public function test_seeded_admin_can_sign_in_and_sign_out(): void
    {
        $user = User::where('email', 'admin@redbook.test')->firstOrFail();

        $this->post('/admin/login', [
            'loginEmail' => 'admin@redbook.test',
            'loginPassword' => 'password',
            'remember' => true,
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);

        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }
}
