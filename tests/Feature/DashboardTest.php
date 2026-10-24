<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk()->assertInertia(fn (Assert $page) => $page->component('admin/Dashboard'));
        $this->assertSame('/admin', route('dashboard', absolute: false));
    }

    public function test_unverified_users_must_verify_email(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get('/admin')->assertRedirect(route('verification.notice'));
    }
}
