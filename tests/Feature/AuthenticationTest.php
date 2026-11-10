<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('KOPKAR POLTEK CWE');
    }

    public function test_admin_can_login_and_redirect_to_staff_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('staff.dashboard'));
    }

    public function test_member_can_login_and_redirect_to_member_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => 'budi',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('member.dashboard'));
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_member_cannot_access_staff_dashboard(): void
    {
        $budi = User::where('username', 'budi')->first();

        $response = $this->actingAs($budi)->get(route('staff.dashboard'));

        $response->assertStatus(403);
    }
}
