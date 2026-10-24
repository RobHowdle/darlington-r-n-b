<?php

namespace Tests\Feature;

use App\Models\ClubSettings;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RnbClubSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return [
            'booking_title' => 'Booking enquiries', 'booking_text' => 'Contact our team',
            'contacts' => [['name' => 'Club organiser', 'phone' => '01234 567890', 'email' => 'club@example.com']],
            'location_title' => 'Find us', 'address' => 'Example Venue, Darlington',
            'phone' => '01234 567890', 'contact_url' => 'https://example.com/contact',
            'facebook_url' => '', 'instagram_url' => '',
        ];
    }

    public function test_club_details_require_a_verified_user(): void
    {
        $this->get('/admin/club-settings')->assertRedirect(route('login'));
        $this->put('/admin/club-settings', $this->payload())->assertRedirect(route('login'));
        $this->actingAs(User::factory()->unverified()->create());
        $this->get('/admin/club-settings')->assertRedirect(route('verification.notice'));
        $this->put('/admin/club-settings', $this->payload())->assertRedirect(route('verification.notice'));
    }

    public function test_saved_details_appear_on_the_homepage_and_survive_reopening(): void
    {
        $this->actingAs(User::factory()->create());
        $this->put('/admin/club-settings', $this->payload())->assertRedirect(route('admin.club.edit'));
        $this->get('/admin/club-settings')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('admin/club/Edit')->where('settings.contacts.0.name', 'Club organiser'));
        $page = new Page(['title' => 'Welcome', 'slug' => 'rnb-welcome', 'status' => 'published',
            'sections' => [['type' => 'rich_text', 'data' => ['body' => '<p>Welcome</p>']]]]);
        $page->forceFill(['site_key' => 'welcome'])->save();
        auth()->logout();
        $this->get('/')->assertOk()->assertSee('Club organiser')->assertSee('Example Venue, Darlington')
            ->assertSee('href="https://example.com/contact"', false)
            ->assertDontSee('mailto:https://example.com/contact', false);
    }

    public function test_invalid_links_and_contact_details_cannot_be_saved(): void
    {
        $this->actingAs(User::factory()->create());
        $data = $this->payload();
        $data['contact_url'] = 'javascript:alert(1)';
        $data['facebook_url'] = 'ftp://example.com';
        $data['contacts'][0]['email'] = 'invalid';
        $this->put('/admin/club-settings', $data)
            ->assertSessionHasErrors(['contact_url', 'facebook_url', 'contacts.0.email']);
        $this->assertDatabaseCount('club_settings', 0);
    }

    public function test_import_preserves_subsequent_edits(): void
    {
        $this->artisan('rnb:import-settings')->assertSuccessful();
        $this->assertCount(2, ClubSettings::current()['contacts']);
        $settings = ClubSettings::query()->firstOrFail();
        $data = $settings->getAttribute('data');
        $data['booking_title'] = 'Edited booking heading';
        $settings->update(['data' => $data]);
        $this->artisan('rnb:import-settings')->assertSuccessful();
        $this->assertDatabaseCount('club_settings', 1);
        $this->assertSame('Edited booking heading', ClubSettings::current()['booking_title']);
    }
}
