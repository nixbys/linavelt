<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_unverified_users_are_redirected_from_admin_dashboard(): void
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('verification.notice'));
    }
}
